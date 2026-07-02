<?php

namespace App\HostingModule\Server;

class EmptyServer {

    public function create($hosting): array
    {
        return [
            'success' => false,
            'message' => 'No compatible server module was selected for this service. Assign an active server group and server before running module create.',
        ];
    }

    public function createPackage($data): array
    {
        return [
            'success' => false,
            'message' => 'No compatible server module was selected for package creation',
        ];
    }

    public function accountSummary($hosting): array
    {
        return [
            'raw_data' => null,
            'processed_data' => null,
        ];
    }

    public function syncConfigOptions($hosting): array
    {
        return [
            'success' => false,
            'message' => 'No compatible server module was selected for configurable option sync',
        ];
    }
    
    public function __call($method, $args): array
    {
        return [
            'success' => false,
            'message' => 'No compatible server module was selected',
        ];
    }
}

 
