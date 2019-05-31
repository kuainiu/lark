<?php

namespace Kuainiu\Lark;

use SocialiteProviders\Manager\SocialiteWasCalled;

class LarkExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('lark', __NAMESPACE__.'\Provider');
    }
}
