<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CallResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'candidate_id' => $this->candidate_id,
            'date' => $this->date,
            'time' => $this->time,
            'meeting_link' => $this->meeting_link,
            'type' => $this->type,
            'tutor_id' => $this->tutor_id,
            'hr_manager_id' => $this->hr_manager_id,
            'candidate' => $this->whenLoaded('candidate', fn() => [
                'id' => $this->candidate->id,
                'name' => $this->candidate->name,
            ]),
            'tutor' => $this->whenLoaded('tutor', fn() => [
                'id' => $this->tutor->id,
                'name' => $this->tutor->name,
            ]),
            'hr_manager' => $this->whenLoaded('hr_manager', fn() => [
                'id' => $this->hr_manager->id,
                'name' => $this->hr_manager->name,
            ]),
        ];
    }
}