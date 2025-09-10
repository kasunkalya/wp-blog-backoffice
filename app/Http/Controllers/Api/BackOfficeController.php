<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WordpressService;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class BackOfficeController extends Controller
{
    protected $wp;

    public function __construct(WordpressService $wp)
    {
        $this->wp = $wp;
    }

    public function index(Request $request)
    {        
        $sort = $request->get('sort', 'priority_desc');
        $query = Post::query();
        if ($sort === 'priority_desc') {
            $query->orderBy('priority', 'desc');
        } elseif ($sort === 'created_desc') {
            $query->orderBy('created_at', 'desc');
        }
        return response()->json($query->paginate(20));
    }

   public function syncFromWordpress()
{
    $user = Auth::user();
    $page = 1;

    while (true) {
        try {
            $posts = $this->wp->getAllPosts($user);;
            if (empty($posts)) break;

            foreach ($posts as $p) {
                Post::updateOrCreate(
                    ['wp_id' => $p['id']],
                    [
                        'title' => $p['title']['rendered'] ?? ($p['title'] ?? ''),
                        'content' => $p['content']['rendered'] ?? ($p['content'] ?? ''),
                        'status' => $p['status'] ?? 'publish',
                    ]
                );
            }

            $page++;
        } catch (\Illuminate\Http\Client\RequestException $e) {           
            $response = $e->response;
            if ($response && $response->status() === 400 && str_contains($response->body(), 'rest_post_invalid_page_number')) {
                break;
            }         
            throw $e;
        }
    }

    return response()->json(['message' => 'Synced']);
}


    public function store(Request $request)
    {
       
        $payload = $request->only(['title','content','status','priority']);
        $user = Auth::user();
        $created = $this->wp->createPost($user, $payload);
        $local = Post::create([
            'wp_id' => $created['id'],
            'title' => $created['title']['rendered'] ?? ($created['title'] ?? $payload['title']),
            'content' => $created['content']['rendered'] ?? ($created['content'] ?? $payload['content']),
            'status' => $created['status'] ?? ($payload['status'] ?? 'publish'),
            'priority' => $created['priority'] ?? ($payload['priority'] ?? '0'),
        ]);

        return response()->json($local);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $payload = $request->only(['title','content','status','priority']);
        $user = Auth::user();

        $updated = $this->wp->updatePost($user, $post->wp_id, $payload);

        $post->update([
            'title' => $updated['title']['rendered'] ?? ($updated['title'] ?? $payload['title']),
            'content' => $updated['content']['rendered'] ?? ($updated['content'] ?? $payload['content']),
            'status' => $updated['status'] ?? ($payload['status'] ?? $post->status),
            'priority' => $updated['priority'] ?? ($payload['priority'] ?? $post->priority),
        ]);

        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();

        $this->wp->deletePost($user, $post->wp_id);
        $post->delete();

        return response()->json(['message' => 'Deleted']);
    }



    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|integer'
        ]);

        $post = Post::findOrFail($id);
        $post->priority = $request->priority;
        $post->save();

        return response()->json(['message' => 'Priority updated successfully']);
    }

}
