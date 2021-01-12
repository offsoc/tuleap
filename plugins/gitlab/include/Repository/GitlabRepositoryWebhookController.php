<?php
/**
 * Copyright (c) Enalean, 2020-Present. All Rights Reserved.
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
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

declare(strict_types=1);

namespace Tuleap\Gitlab\Repository;

use DateTimeImmutable;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;
use Tuleap\Gitlab\Repository\Webhook\EventNotAllowedException;
use Tuleap\Gitlab\Repository\Webhook\MissingKeyException;
use Tuleap\Gitlab\Repository\Webhook\RepositoryNotFoundException;
use Tuleap\Gitlab\Repository\Webhook\Secret\SecretChecker;
use Tuleap\Gitlab\Repository\Webhook\Secret\SecretHeaderNotFoundException;
use Tuleap\Gitlab\Repository\Webhook\Secret\SecretHeaderNotMatchingException;
use Tuleap\Gitlab\Repository\Webhook\Secret\SecretNotDefinedException;
use Tuleap\Gitlab\Repository\Webhook\WebhookActions;
use Tuleap\Gitlab\Repository\Webhook\WebhookDataExtractor;
use Tuleap\Gitlab\Repository\Webhook\WebhookRepositoryRetriever;
use Tuleap\Request\DispatchablePSR15Compatible;
use Tuleap\Request\DispatchableWithRequestNoAuthz;
use Tuleap\Gitlab\Repository\Webhook\EmptyBranchNameException;
use Tuleap\Gitlab\Repository\Webhook\MissingEventKeysException;

class GitlabRepositoryWebhookController extends DispatchablePSR15Compatible implements DispatchableWithRequestNoAuthz
{
    /**
     * @var WebhookDataExtractor
     */
    private $webhook_data_extractor;

    /**
     * @var ResponseFactoryInterface
     */
    private $response_factory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var WebhookRepositoryRetriever
     */
    private $webhook_repository_retriever;

    /**
     * @var SecretChecker
     */
    private $secret_checker;

    /**
     * @var WebhookActions
     */
    private $webhook_actions;

    public function __construct(
        WebhookDataExtractor $webhook_data_extractor,
        WebhookRepositoryRetriever $webhook_repository_retriever,
        SecretChecker $secret_checker,
        WebhookActions $webhook_actions,
        LoggerInterface $logger,
        ResponseFactoryInterface $response_factory,
        EmitterInterface $emitter,
        MiddlewareInterface ...$middleware_stack
    ) {
        parent::__construct($emitter, ...$middleware_stack);

        $this->webhook_data_extractor       = $webhook_data_extractor;
        $this->webhook_repository_retriever = $webhook_repository_retriever;
        $this->secret_checker               = $secret_checker;
        $this->response_factory             = $response_factory;
        $this->logger                       = $logger;
        $this->webhook_actions              = $webhook_actions;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info("GitLab webhook received.");
        $current_time = new DateTimeImmutable();

        try {
            $webhook_data = $this->webhook_data_extractor->retrieveWebhookData(
                $request
            );

            $gitlab_repository = $this->webhook_repository_retriever->retrieveRepositoryFromWebhookData(
                $webhook_data
            );

            $this->secret_checker->checkSecret(
                $gitlab_repository,
                $request
            );

            $this->webhook_actions->performActions(
                $gitlab_repository,
                $webhook_data,
                $current_time
            );

            return $this->response_factory->createResponse(200);
        } catch (RepositoryNotFoundException $exception) {
            $this->logger->error($exception->getMessage());
            return $this->response_factory->createResponse(404);
        } catch (
            MissingKeyException |
            EventNotAllowedException |
            SecretHeaderNotFoundException |
            SecretNotDefinedException |
            EmptyBranchNameException |
            SecretHeaderNotMatchingException |
            MissingEventKeysException $exception
        ) {
            $this->logger->error($exception->getMessage());
            return $this->response_factory->createResponse(400);
        }
    }
}
