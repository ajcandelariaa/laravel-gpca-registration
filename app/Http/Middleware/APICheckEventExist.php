<?php

namespace App\Http\Middleware;

use App\Models\Event;
use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class APICheckEventExist
{
    use HttpResponses;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $eventId = (int) $request->route('eventId');
        $eventCategory = $request->route('eventCategory');

        $event = Event::where('id', $eventId)->where('category', $eventCategory)->where('active', true)->exists();

        if (!$event) {
            Log::warning("Event not found with ID $eventId and category $eventCategory");
            return $this->error(null, "Event doesn't exist", 404);
        }
        return $next($request);
    }
}
