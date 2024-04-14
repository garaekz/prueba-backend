<?php

namespace App\Actions;

use App\Models\UserComment;

class StoreCommentAction
{
    /**
     * Execute the action to store a comment.
     *
     * @param array $data The data to store.
     * @param int|null $id The ID of the comment to update (optional).
     * 
     * @return null|array The stored or updated comment data or null.
     */
    public function execute(array $data, $id = null)
    {
        if (isset($data['comment'])) {
            $data['coment_text'] = $data['comment'];
            unset($data['comment']);
        }

        if ($id) {
            $comment = UserComment::update($id, $data);
        } else {
            if (!isset($data['likes'])) {
                $data['likes'] = 0;
            }

            $comment = UserComment::create($data);
        }

        return $comment;
    }
}
