<?php
/**
 * Copyright (c) Enalean, 2012-Present. All Rights Reserved.
 * Copyright (c) Xerox Corporation, Codendi Team, 2001-2009. All rights reserved
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */


/**
* System Event classes
*
*/
class SystemEvent_SYSTEM_CHECK extends SystemEvent
{
    /**
     * Verbalize the parameters so they are readable and much user friendly in
     * notifications
     *
     * @param bool $with_link true if you want links to entities. The returned
     * string will be html instead of plain/text
     *
     * @return string
     */
    public function verbalizeParameters($with_link)
    {
        return '-';
    }

    /**
     * Process stored event
     */
    public function process()
    {
        $backendSystem = Backend::instance('System');
        \assert($backendSystem instanceof BackendSystem);
        $backendAliases = Backend::instance('Aliases');
        \assert($backendAliases instanceof BackendAliases);
        $backendSVN         = Backend::instanceSVN();
        $backendCVS         = Backend::instanceCVS();
        $backendMailingList = Backend::instance('MailingList');
        \assert($backendMailingList instanceof BackendMailingList);

        //TODO:
        // User: unix_status vs status??
        // Private project: if codeaxadm is not member of the project: check access to SVN (incl. ViewVC), CVS, Web...
        // CVS Watch?
        // TODO: log event in syslog?
        // TODO: check that there is no pending event??? What about lower priority events??

        // First, force NSCD refresh to be sure that uid/gid will exist on next
        // actions

        $backendSystem->flushNscdAndFsCache();

        // Force global updates: aliases, CVS roots, SVN roots
        $backendAliases->setNeedUpdateMailAliases();

        // Remove temporary files generated by aborted CVS commits
        $backendCVS->cleanup();

        // Check mailing lists
        // (re-)create missing ML
        $mailinglistdao = new MailingListDao();
        $dar            = $mailinglistdao->searchAllActiveML();
        foreach ($dar as $row) {
            $list = new MailingList($row);
            if (! $backendMailingList->listExists($list)) {
                $backendMailingList->createList($list->getId());
            }
            // TODO what about lists that changed their setting (description, public/private) ?
        }

        $errors = [];

        $project_manager = ProjectManager::instance();
        foreach ($project_manager->getProjectsByStatus(Project::STATUS_ACTIVE) as $project) {
            try {
                $backendSystem->systemCheck($project);
            } catch (Exception $exception) {
                $errors[] = $exception->getMessage();
            }

            try {
                $backendCVS->systemCheck($project);
            } catch (Exception $exception) {
                $errors[] = $exception->getMessage();
            }

            try {
                $backendSVN->systemCheck($project);
            } catch (Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        $backend_logger = BackendLogger::getDefaultLogger();
        $logger         = new SystemCheckLogger($backend_logger, 'system_check');

        if ($backend_logger instanceof BackendLogger) {
            $backend_logger->restoreOwnership($backendSystem);
        }

        // remove deleted releases and released files
        // This is done after the verification all the project directories to avoid
        // bad surprises when moving files
        if (! $backendSystem->cleanupFRS()) {
            $errors[] = 'An error occurred while moving FRS files';
        }

        $this->warnWhenThereIsTooMuchDelayInWorkerEventsProcessing($logger);

        try {
            EventManager::instance()->processEvent(
                Event::PROCCESS_SYSTEM_CHECK,
                [
                    'logger' => $logger,
                ]
            );
        } catch (Exception $exception) {
            $errors[] = $exception->getMessage();
        }

        $this->expireRestTokens(UserManager::instance());

        if ($logger->hasWarnings()) {
            $this->warning($logger->getAllWarnings());
        } elseif (count($errors) > 0) {
            $this->error(implode("\n", $errors));
            return false;
        } else {
            $this->done();
            return true;
        }
    }

    public function expireRestTokens(UserManager $user_manager)
    {
        $token_dao     = new Rest_TokenDao();
        $token_factory = new Rest_TokenFactory($token_dao);
        $token_manager = new Rest_TokenManager($token_dao, $token_factory, $user_manager);

        $token_manager->expireOldTokens();
    }

    private function warnWhenThereIsTooMuchDelayInWorkerEventsProcessing(\Psr\Log\LoggerInterface $logger): void
    {
        $queue = (new \Tuleap\Queue\QueueFactory($logger))->getPersistentQueue(Tuleap\Queue\Worker::EVENT_QUEUE_NAME);

        $queue_supervisor = new \Tuleap\Queue\QueueSupervisor($queue, $logger);
        $queue_supervisor->warnWhenThereIsTooMuchDelayInWorkerEventsProcessing(new DateTimeImmutable());
    }
}
