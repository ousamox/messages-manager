<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/5/18
 * Time: 17:54
 */

namespace Ousamox\MessagesManager\Models;


class MessageFile extends AppModel
{
    protected $fillable = [
        'title',
    ];

    const FILE_DIRECTORY = 'msg_files/';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('omm.models.message_file.table_name');
    }

    public function getTable()
    {
        return config('omm.models.message_file.table_name');
    }

    public function getCreatedAtAttribute() {
        $sent_at = $this->attributes['created_at'];
        if(is_object($sent_at)) {
            /** @var \Carbon\Carbon $sent_at */
            $sent_at = $sent_at->format("Y-m-d H:i:s");
        }
        return (strtotime($sent_at));
    }

    public function message() {
        return $this->belongsTo(Message::class, "message_id");
    }

    public static function upload($data) {
        $documents = $data['documents'];
        $returned = [];
        foreach ($documents as $document){
            $storedFile = $document->store('public/'.self::FILE_DIRECTORY);
            $explodeSFile = explode('/',$storedFile);
            $file = end($explodeSFile);
            $explodeFileName = explode('.',$file);
            $doc = new self();
            $doc->forceFill([
                'title' => $data['titre'],
                'filename' => $explodeFileName[0],
                'extension' => $explodeFileName[1],
                'path' => '/'.self::FILE_DIRECTORY.$file,
                'size' => \File::size(storage_path('app/public/'.self::FILE_DIRECTORY.$file)),
            ]);
            $doc->message()->associate($data['message_id']);
            $doc->save();
            $returned [] = [
                'id' => $doc->id,
                'titre' => $data['titre'],
                'filename' => $explodeFileName[0],
                'extension' => $explodeFileName[1],
                'path' => '/'.self::FILE_DIRECTORY.$file,
                'size' => \File::size(storage_path('app/public/'.self::FILE_DIRECTORY.$file))
            ];
        }
        return $returned;
    }

}