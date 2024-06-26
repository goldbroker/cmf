<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\MenuBundle\Unit\Voter;

use Symfony\Cmf\Bundle\MenuBundle\Voter\RequestContentIdentityVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestContentIdentityVoterTest extends RequestContentIdentityVoterTestCase
{
    protected function buildVoter(Request $request)
    {
        $requestStack = new RequestStack();
        $requestStack->push($request);

        return new RequestContentIdentityVoter('_content', $requestStack);
    }

    public function testSkipsWhenNoRequestIsAvailable()
    {
        $voter = new RequestContentIdentityVoter('_content', new RequestStack());

        $this->assertNull($voter->matchItem($this->createItem()));
    }
}
