<?php
/**
 * Copyright (c) Enalean, 2015-Present. All rights reserved
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/
 */

namespace Tuleap\SVN\ViewVC;

use Codendi_HTMLPurifier;
use CrossReferenceFactory;
use EventManager;
use HTTPRequest;
use Project;
use ReferenceManager;
use Tuleap\Error\ProjectAccessSuspendedController;
use Tuleap\Project\CheckProjectAccess;
use Tuleap\svn\Event\GetSVNLoginNameEvent;
use Tuleap\SVN\Repository\Repository;

class ViewVCProxy
{
    /**
     * @var AccessHistorySaver
     */
    private $access_history_saver;
    /**
     * @var EventManager
     */
    private $event_manager;
    /**
     * @var ProjectAccessSuspendedController
     */
    private $access_suspended_controller;

    public function __construct(
        AccessHistorySaver $access_history_saver,
        EventManager $event_manager,
        ProjectAccessSuspendedController $access_suspended_controller,
        private CheckProjectAccess $check_project_access,
    ) {
        $this->access_history_saver        = $access_history_saver;
        $this->event_manager               = $event_manager;
        $this->access_suspended_controller = $access_suspended_controller;
    }

    private function displayViewVcHeader(HTTPRequest $request)
    {
        $request_uri = $request->getFromServer('REQUEST_URI');

        if (strpos($request_uri, "annotate=") !== false) {
            return true;
        }

        if (
            $this->isViewingPatch($request) ||
            $this->isCheckoutingFile($request) ||
            strpos($request_uri, "view=graphimg") !== false ||
            strpos($request_uri, "view=redirect_path") !== false ||
            // ViewVC will redirect URLs with "&rev=" to "&revision=". This is needed by Hudson.
            strpos($request_uri, "&rev=") !== false
        ) {
            return false;
        }

        if (
            strpos($request_uri, "/?") === false &&
            strpos($request_uri, "&r1=") === false &&
            strpos($request_uri, "&r2=") === false &&
            strpos($request_uri, "view=") === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isViewingPatch(HTTPRequest $request)
    {
        $request_uri = $request->getFromServer('REQUEST_URI');
        return strpos($request_uri, "view=patch") !== false;
    }

    /**
     * @return bool
     */
    private function isCheckoutingFile(HTTPRequest $request)
    {
        $request_uri = $request->getFromServer('REQUEST_URI');
        return strpos($request_uri, "view=co") !== false;
    }

    private function buildQueryString(HTTPRequest $request)
    {
        parse_str($request->getFromServer('QUERY_STRING'), $query_string_parts);
        unset($query_string_parts['roottype']);
        return http_build_query($query_string_parts);
    }

    private function escapeStringFromServer(HTTPRequest $request, $key)
    {
        $string = $request->getFromServer($key);

        return escapeshellarg($string);
    }

    private function setLocaleOnFileName($path)
    {
        $current_locales = setlocale(LC_ALL, "0");
        // to allow $path filenames with French characters
        setlocale(LC_CTYPE, "en_US.UTF-8");

        $encoded_path = escapeshellarg($path);
        setlocale(LC_ALL, $current_locales);

        return $encoded_path;
    }

    private function setLocaleOnCommand($command, &$return_var)
    {
        ob_start();
        putenv("LC_CTYPE=en_US.UTF-8");
        passthru($command, $return_var);

        return ob_get_clean();
    }

    private function getViewVcLocationHeader($location_line)
    {
        // Now look for 'Location:' header line (e.g. generated by 'view=redirect_pathrev'
        // parameter, used when browsing a directory at a certain revision number)
        $location_found = false;

        while ($location_line && ! $location_found && strlen($location_line) > 1) {
            $matches = [];

            if (preg_match('/^Location:(.*)$/', $location_line, $matches)) {
                return trim($matches[1]);
            }

            $location_line = strtok("\n\t\r\0\x0B");
        }

        return false;
    }

    private function getPurifier()
    {
        return Codendi_HTMLPurifier::instance();
    }

    /**
     * @return string
     */
    private function getUsername(\PFUser $user, Project $project)
    {
        $event = new GetSVNLoginNameEvent($user, $project);
        $this->event_manager->processEvent($event);
        return $event->getUsername();
    }

    /**
     * @return string
     */
    private function getPythonLauncher()
    {
        if (file_exists('/opt/rh/python27/root/usr/bin/python')) {
            return "LD_LIBRARY_PATH='/opt/rh/python27/root/usr/lib64' /opt/rh/python27/root/usr/bin/python";
        }
        return '/usr/bin/python';
    }

    public function getContent(HTTPRequest $request, \PFUser $user, Repository $repository, string $path)
    {
        if ($user->isAnonymous()) {
            return dgettext('tuleap-svn', 'You can not browse the repository without being logged.');
        }

        $project = $repository->getProject();
        if ($project->isSuspended() && ! $user->isSuperUser()) {
            $this->access_suspended_controller->displayError($user);
            exit();
        }

        try {
            $this->check_project_access->checkUserCanAccessProject($user, $project);
        } catch (\Project_AccessException $exception) {
            return $this->getPermissionDeniedError($project);
        }

        $this->access_history_saver->saveAccess($user, $repository);

        $command = 'REMOTE_USER_ID=' . escapeshellarg($user->getId()) . ' ' .
            'REMOTE_USER=' . escapeshellarg($this->getUsername($user, $project)) . ' ' .
            'PATH_INFO=' . $this->setLocaleOnFileName($path) . ' ' .
            'QUERY_STRING=' . escapeshellarg($this->buildQueryString($request)) . ' ' .
            'SCRIPT_NAME=/plugins/svn ' .
            'HTTP_ACCEPT_ENCODING=' . $this->escapeStringFromServer($request, 'HTTP_ACCEPT_ENCODING') . ' ' .
            'HTTP_ACCEPT_LANGUAGE=' . $this->escapeStringFromServer($request, 'HTTP_ACCEPT_LANGUAGE') . ' ' .
            'TULEAP_PROJECT_NAME=' . escapeshellarg($repository->getProject()->getUnixNameMixedCase()) . ' ' .
            'TULEAP_REPO_NAME=' . escapeshellarg($repository->getName()) . ' ' .
            'TULEAP_REPO_PATH=' . escapeshellarg($repository->getSystemPath()) . ' ' .
            'TULEAP_FULL_REPO_NAME=' . escapeshellarg($repository->getFullName()) . ' ' .
            'TULEAP_USER_IS_SUPER_USER=' . escapeshellarg($user->isSuperUser() ? '1' : '0') . ' ' .
            $this->getPythonLauncher() . ' ' . __DIR__ . '/../../../bin/viewvc-epel.cgi 2>&1';

        $content = $this->setLocaleOnCommand($command, $return_var);

        if ($return_var === 128) {
            return $this->getPermissionDeniedError($project);
        }

        [$headers, $body] = http_split_header_body($content);

        $content_type_line = strtok($content, "\n\t\r\0\x0B");

        $content = substr($content, strpos($content, $content_type_line));

        $location_line   = strtok($content, "\n\t\r\0\x0B");
        $viewvc_location = $this->getViewVcLocationHeader($location_line);

        if ($viewvc_location) {
            $GLOBALS['Response']->redirect($viewvc_location);
        }

        $parse = $this->displayViewVcHeader($request);
        if ($parse) {
            //parse the html doc that we get from viewvc.
            //remove the http header part as well as the html header and
            //html body tags
            $cross_ref = "";
            if ($request->get('revision')) {
                $crossref_fact = new CrossReferenceFactory(
                    $repository->getName() . "/" . $request->get('revision'),
                    ReferenceManager::REFERENCE_NATURE_SVNREVISION,
                    $repository->getProject()->getID()
                );
                $crossref_fact->fetchDatas();
                if ($crossref_fact->getNbReferences() > 0) {
                    $cross_ref .= '<div class="viewvc-epel-references">';
                    $cross_ref .= '<h4>' . $GLOBALS['Language']->getText('cross_ref_fact_include', 'references') . '</h4>';
                    $cross_ref .= $crossref_fact->getHTMLDisplayCrossRefs();
                    $cross_ref .= '</div>';
                }

                $body = str_replace(
                    "<h4>Modified files</h4>",
                    $cross_ref . "<h4>Modified files</h4>",
                    $body
                );
            }

            // Now insert references, and display
            return util_make_reference_links(
                $body,
                $request->get('group_id')
            );
        } else {
            if ($this->isViewingPatch($request)) {
                header('Content-Type: text/plain');
            } else {
                header('Content-Type: application/octet-stream');
            }
            header('X-Content-Type-Options: nosniff');
            header('Content-Disposition: attachment');

            echo $body;
            exit();
        }
    }

    private function getPermissionDeniedError(Project $project)
    {
        $purifier = $this->getPurifier();
        $url      = session_make_url("/project/memberlist.php?group_id=" . urlencode((string) $project->getID()));

        $title  = $purifier->purify($GLOBALS['Language']->getText('svn_viewvc', 'access_denied'));
        $reason = $GLOBALS['Language']->getText('svn_viewvc', 'acc_den_comment', $purifier->purify($url));

        return '<link rel="stylesheet" href="/viewvc-theme-tuleap/style.css">
            <div class="tuleap-viewvc-header">
                <h3>' . $title . '</h3>
                ' . $reason . '
            </div>';
    }

    public function getBodyClass()
    {
        return 'viewvc-epel';
    }
}
