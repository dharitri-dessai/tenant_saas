<?php

namespace App\Message;

final class TenantSubscriptionEmailMessage
{
    /*
     * Properties and methods you need
     * to hold the data for this message class.
     */

    private $tenantId;

    public function __construct(int $tenantId)
    {
        $this->tenantId = $tenantId;
    }

   public function getTenantId(): int
   {
       return $this->tenantId;
   }
}
