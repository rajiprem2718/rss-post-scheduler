<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rss_model extends CI_Model {

    public function insert_post($data) {
        $this->db->insert('posts', $data);
    }

    public function get_posts($limit, $offset) {
        $this->db->order_by('priority', 'ASC');
        $query = $this->db->get('posts', $limit, $offset);
        return $query->result();
    }

    public function get_post_platform_ids($post_id)
{
    $rows = $this->db->select('platform_id')
                     ->from('post_platforms')
                     ->where('post_id', $post_id)
                     ->get()
                     ->result();

    $ids = [];
    foreach ($rows as $r) {
        $ids[] = (string)$r->platform_id; // string for easier JS compare
    }
    return $ids;
}


    public function count_posts() {
        return $this->db->count_all('posts');
    }

    public function update_priority($id, $new_priority) {
        // Get current priority
        $post = $this->db->where('id', $id)->get('posts')->row();
        if (!$post) return;

        $current_priority = (int)$post->priority;
        $new_priority     = (int)$new_priority;

        if ($new_priority == $current_priority) {
            return;
        }

        $this->db->trans_start();

        if ($new_priority < $current_priority) {
            // Move up: shift others down
            $this->db->query("
                UPDATE posts 
                SET priority = priority + 1
                WHERE priority >= ? AND priority < ? AND id != ?
            ", [$new_priority, $current_priority, $id]);
        } else {
            // Move down: shift others up
            $this->db->query("
                UPDATE posts 
                SET priority = priority - 1
                WHERE priority <= ? AND priority > ? AND id != ?
            ", [$new_priority, $current_priority, $id]);
        }

        $this->db->where('id', $id)->update('posts', ['priority' => $new_priority]);

        $this->db->trans_complete();
    }

    public function delete_post($id) {
        $post = $this->db->where('id', $id)->get('posts')->row();
        if (!$post) return;

        $priority = (int)$post->priority;

        $this->db->trans_start();

        $this->db->where('id', $id)->delete('posts');

        $this->db->query("
            UPDATE posts
            SET priority = priority - 1
            WHERE priority > ?
        ", [$priority]);

        $this->db->where('post_id', $id)->delete('post_platforms');

        $this->db->trans_complete();
    }

    public function get_platforms() {
        $query = $this->db->get('platforms');
        return $query->result();
    }

    public function assign_platforms($post_id, $platforms) {
        $this->db->where('post_id', $post_id)->delete('post_platforms');

        if (is_array($platforms)) {
            foreach ($platforms as $p) {
                $this->db->insert('post_platforms', [
                    'post_id'     => $post_id,
                    'platform_id' => (int)$p
                ]);
            }
        }
    }

    public function get_posts_by_platform($platform_id = null) {
        if (!$platform_id) {
            $this->db->order_by('priority', 'ASC');
            return $this->db->get('posts')->result();
        }

        $this->db->select('posts.*');
        $this->db->from('posts');
        $this->db->join('post_platforms', 'posts.id = post_platforms.post_id');
        $this->db->where('post_platforms.platform_id', $platform_id);
        $this->db->order_by('posts.priority', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }
}
