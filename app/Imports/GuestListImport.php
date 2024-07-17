<?php

namespace App\Imports;

use App\Models\guestList;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuestListImport implements ToModel, WithHeadingRow
{
    protected $invitationId;

    public function __construct($invitationId)
    {
        $this->invitationId = $invitationId;
    }
    public function model(array $row)
    {
        return new guestList([
            'invitation_id' => $this->invitationId,
            'name' => $row['name'],
            'no_phone' => $row['no_phone'],
            'attendance' => $row['attendance'],
        ]);
    }
}
