<?php
/**
 * Copyright (c) Enalean, 2023 - Present. All Rights Reserved.
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

declare(strict_types=1);

namespace Tuleap\InviteBuddy;

use Tuleap\Authentication\SplitToken\SplitToken;
use Tuleap\Authentication\SplitToken\SplitTokenVerificationString;
use Tuleap\Authentication\SplitToken\SplitTokenVerificationStringHasher;
use Tuleap\DB\DBFactory;
use Tuleap\Test\PHPUnit\TestCase;

class InvitationDaoTest extends TestCase
{
    private InvitationDao $dao;

    protected function setUp(): void
    {
        $this->dao = new InvitationDao(new SplitTokenVerificationStringHasher());
    }

    protected function tearDown(): void
    {
        DBFactory::getMainTuleapDBConnection()->getDB()->run("DELETE FROM invitations");
    }

    public function testSavesInvitationWithVerifier(): void
    {
        $verifier = SplitTokenVerificationString::generateNewSplitTokenVerificationString();

        $id = $this->dao->create(1234567890, 101, "jdoe@example.com", null, null, $verifier);

        $invitation = $this->dao->searchBySplitToken(new SplitToken($id, $verifier));
        self::assertEquals($id, $invitation->id);
        self::assertEquals(101, $invitation->from_user_id);
        self::assertEquals('jdoe@example.com', $invitation->to_email);
    }

    public function testExceptionWhenTokenCannotBeVerified(): void
    {
        $verifier = SplitTokenVerificationString::generateNewSplitTokenVerificationString();

        $id = $this->dao->create(1234567890, 101, "jdoe@example.com", null, null, $verifier);

        $invalid_verifier = SplitTokenVerificationString::generateNewSplitTokenVerificationString();

        $this->expectException(InvalidInvitationTokenException::class);
        $this->dao->searchBySplitToken(new SplitToken($id, $invalid_verifier));
    }

    public function testSaveJustCreatedUserThanksToInvitationWhenNoSpecificInvitationIsUsed(): void
    {
        $this->createBunchOfInvitations();

        self::assertFalse($this->dao->hasUsedAnInvitationToRegister(201));

        $this->dao->saveJustCreatedUserThanksToInvitation('alice@example.com', 201, null);

        self::assertFalse($this->dao->hasUsedAnInvitationToRegister(201));

        self::assertEquals(
            [201, null, 201],
            DBFactory::getMainTuleapDBConnection()->getDB()->column("SELECT created_user_id FROM invitations ORDER BY id"),
        );
        self::assertEquals(
            [Invitation::STATUS_SENT, Invitation::STATUS_SENT, Invitation::STATUS_SENT],
            DBFactory::getMainTuleapDBConnection()->getDB()->column("SELECT status FROM invitations ORDER BY id"),
        );
    }

    public function testSaveJustCreatedUserThanksToInvitationWhenAGivenInvitationIsUsed(): void
    {
        [, , $second_invitation_to_alice_id] = $this->createBunchOfInvitations();

        self::assertFalse($this->dao->hasUsedAnInvitationToRegister(201));

        $this->dao->saveJustCreatedUserThanksToInvitation('alice@example.com', 201, $second_invitation_to_alice_id);

        self::assertTrue($this->dao->hasUsedAnInvitationToRegister(201));

        self::assertEquals(
            [201, null, 201],
            DBFactory::getMainTuleapDBConnection()->getDB()->column("SELECT created_user_id FROM invitations ORDER BY id"),
        );
        self::assertEquals(
            [Invitation::STATUS_SENT, Invitation::STATUS_SENT, Invitation::STATUS_USED],
            DBFactory::getMainTuleapDBConnection()->getDB()->column("SELECT status FROM invitations ORDER BY id"),
        );
    }

    public function createBunchOfInvitations(): array
    {
        $verifier_1 = SplitTokenVerificationString::generateNewSplitTokenVerificationString();
        $verifier_2 = SplitTokenVerificationString::generateNewSplitTokenVerificationString();
        $verifier_3 = SplitTokenVerificationString::generateNewSplitTokenVerificationString();

        $first_invitation_to_alice_id  = $this->dao->create(1234567890, 101, "alice@example.com", null, null, $verifier_1);
        $first_invitation_to_bob_id    = $this->dao->create(1234567890, 102, "bob@example.com", null, null, $verifier_2);
        $second_invitation_to_alice_id = $this->dao->create(1234567890, 103, "alice@example.com", null, null, $verifier_3);

        $this->dao->markAsSent($first_invitation_to_alice_id);
        $this->dao->markAsSent($first_invitation_to_bob_id);
        $this->dao->markAsSent($second_invitation_to_alice_id);

        return [$first_invitation_to_alice_id, $first_invitation_to_bob_id, $second_invitation_to_alice_id];
    }
}
