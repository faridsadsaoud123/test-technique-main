<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        Request::validate([
            'starts_at' => ['nullable', 'date:Y-m-d'],
            'ends_at' => ['nullable', 'date:Y-m-d'],
        ]);

        return Inertia::render('Events/Index', [
            'starts_at' => Request::get('starts_at'),
            'ends_at' => Request::get('ends_at'),
            'events' => Event::isBetween(Request::get('starts_at'), Request::get('ends_at'))->orderByDate()->get()
        ]);
    }

    public function store()
{
    $data = Request::validate([
        'title' => ['required', 'max:255'],
        'starts_at' => ['required', 'date_format:Y-m-d H:i'],
        'ends_at' => ['nullable', 'date_format:Y-m-d H:i']
    ]);

    Event::create([
        'title' => $data['title'],
        'starts_at' => Carbon::createFromFormat('Y-m-d H:i', $data['starts_at']),
        'ends_at' => $data['ends_at'] ? Carbon::createFromFormat('Y-m-d H:i', $data['ends_at']) : null
    ]);

    return Redirect::back();
}

public function update(Event $event)
{
    $data = Request::validate([
        'title' => ['required', 'max:255'],
        'starts_at' => ['required', 'date_format:Y-m-d H:i'],
        'ends_at' => ['nullable', 'date_format:Y-m-d H:i']
    ]);

    $event->update([
        'title' => $data['title'],
        'starts_at' => Carbon::createFromFormat('Y-m-d H:i', $data['starts_at']),
        'ends_at' => $data['ends_at'] ? Carbon::createFromFormat('Y-m-d H:i', $data['ends_at']) : null
    ]);

    return Redirect::back();
}
    public function destroy(Event $event)
    {
        $event->delete();

        return Redirect::back();
    }
}
