<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rss extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Rss_model');
        $this->load->helper(['url', 'form']);
    }

    public function index() {
        $this->load->view('rss_import');
    }

    public function import() {
        $url  = $this->input->post('url');
        $sort = $this->input->post('sort'); // ASC / DESC

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $data['error'] = 'Invalid RSS URL';
            $this->load->view('rss_import', $data);
            return;
        }

        $feed = @simplexml_load_file($url);

        if (!$feed) {
            $data['error'] = 'Unable to fetch RSS feed!';
            $this->load->view('rss_import', $data);
            return;
        }

        $items = [];
        if (isset($feed->channel->item)) {
            foreach ($feed->channel->item as $item) {
                $items[] = [
                    'title'    => (string)$item->title,
                    'content'  => (string)$item->description,
                    'pub_date' => date('Y-m-d H:i:s', strtotime((string)$item->pubDate)),
                ];
            }
        }

        if (empty($items)) {
            $data['error'] = 'No items found in RSS feed.';
            $this->load->view('rss_import', $data);
            return;
        }

        // Sort items based on pub_date
        usort($items, function($a, $b) use ($sort) {
            if ($sort === 'ASC') {
                return strtotime($a['pub_date']) - strtotime($b['pub_date']);
            }
            return strtotime($b['pub_date']) - strtotime($a['pub_date']);
        });

        // Save to DB with priority
        $priority = 1;
        foreach ($items as $item) {
            $char_count = mb_strlen($item['title'] . ' ' . $item['content'], 'UTF-8');

            $this->Rss_model->insert_post([
                'title'      => $item['title'],
                'content'    => $item['content'],
                'char_count' => $char_count,
                'pub_date'   => $item['pub_date'],
                'priority'   => $priority++
            ]);
        }

        redirect('rss/posts');
    }

    public function posts($page = 1) {
        $limit  = 10;
        $page   = max(1, (int)$page);
        $offset = ($page - 1) * $limit;
        $total_posts          = $this->Rss_model->count_posts();
        $data['posts']        = $this->Rss_model->get_posts($limit, $offset);
        $data['platforms']    = $this->Rss_model->get_platforms();
        $data['current_page'] = $page;
        $data['total_pages']  = ($total_posts > 0) ? ceil($total_posts / $limit) : 1;

        // attach selected platform IDs to each post
        foreach ($data['posts'] as $p) {
            $p->platform_ids = $this->Rss_model->get_post_platform_ids($p->id);
}

$this->load->view('post_list', $data);

    }

    public function update_priority() {
        $id           = (int)$this->input->post('id');
        $new_priority = (int)$this->input->post('priority');

        if ($id > 0 && $new_priority > 0) {
            $this->Rss_model->update_priority($id, $new_priority);
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function delete($id) {
        $id = (int)$id;
        if ($id > 0) {
            $this->Rss_model->delete_post($id);
        }
        redirect('rss/posts');
    }

    public function assign_platform() {
        $post_id   = (int)$this->input->post('post_id');
        $platforms = $this->input->post('platforms');

        if ($post_id > 0) {
            $this->Rss_model->assign_platforms($post_id, $platforms);
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function dashboard() {
        $platform_id = $this->input->get('platform');
        $platform_id = $platform_id ? (int)$platform_id : null;

        $data['platforms'] = $this->Rss_model->get_platforms();
        $data['posts']     = $this->Rss_model->get_posts_by_platform($platform_id);
        $data['selected']  = $platform_id;

        $this->load->view('dashboard', $data);
    }

    public function social_dashboard() {
    // can be 'all' or a specific platform id
    $platform = $this->input->get('platform');

    if ($platform && $platform !== 'all') {
        $platform_id = (int)$platform;
    } else {
        $platform_id = null; // null => all posts
        $platform    = 'all';
    }

    $data['platforms'] = $this->Rss_model->get_platforms();
    $data['posts']     = $this->Rss_model->get_posts_by_platform($platform_id);
    $data['selected']  = $platform;

    // attach all platform ids for each post so we can display badges
    foreach ($data['posts'] as $p) {
        $p->platform_ids = $this->Rss_model->get_post_platform_ids($p->id);
    }

    $this->load->view('social_dashboard', $data);
}


}
