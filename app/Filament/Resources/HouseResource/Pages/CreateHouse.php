<?php

namespace App\Filament\Resources\HouseResource\Pages;

use App\Filament\Resources\HouseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHouse extends CreateRecord
{
    protected static string $resource = HouseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()?->hasRole('developer') && ! auth()->user()?->hasRole('admin')) {
            $data['developer_id'] = auth()->id();
        }

        return $data;
    }
}
