<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Post $post */
        $post = $request->route()->parameter('post');

        if ($post->user_id !== auth()->id()) {
            return responseFailed(
                __('errors.post.access_denied'),
                'Post post belongs to another user',
                Response::HTTP_FORBIDDEN
            );
        }

        return $next($request);
    }
}
