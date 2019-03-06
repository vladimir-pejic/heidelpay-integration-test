<?php
namespace App\Services;

use App\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TenantManager {
    /*
     * @var null|App\Tenant
     */
    private $tenant;

    public function setTenant(?User $tenant) {
        $this->tenant = $tenant;
        return $this;
    }

    public function getTenant(): ?User {
        return $this->tenant;
    }

    public function loadTenant(string $identifier): bool {
        $tenant = User::query()->where('slug', '=', $identifier)->first();

        if ($tenant) {
            $this->setTenant($tenant);
            return true;
        }

        return false;
    }
}