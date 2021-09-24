<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $unread_noti = auth()->user()->unreadNotifications()->count();

        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'account_numbers' => $this->wallet ? $this->wallet->account_numbers : '' ,
            'amount' => $this->wallet ? number_format($this->wallet->amount) : '' ,
            'hash_value' => $this->phone,
            'unread_notification' =>$unread_noti ,
            'profile'=> asset('/img/user.png')
        ];
    }
}
