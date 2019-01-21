<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['message_id', 'media'];
    /**
     * Get the message this attchment belongs to
     */
    public function message(){
        return $this->belongsTo(Message::class);
    }
}
