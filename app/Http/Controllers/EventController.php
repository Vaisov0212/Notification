<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Time;
use Illuminate\Support\Facades\Validator;

use App\Models\Event;
class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $events = Event::where('user_id', Auth::id())
                   ->orderBy('created_at', 'desc')
                   ->get();

    return view('event.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('event.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
         $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // 'colors' => 'required|string|max:7', // hex color format
            'repeat_type' => 'required|in:daily,weekly,monthly',
            'repeat_interval' => 'nullable|integer|min:1',
            'weekly_days' => 'nullable|array',
            'monthly_days' => 'nullable|array',
            'event_times' => 'required|array|min:1',
            'event_times.*' => 'required|string' // sodda format validation
        ]);

$repeatDays = null;
switch ($request->repeat_type) {
    case 'weekly':
        $repeatDays = $request->weekly_days; // Bu yerda weekly_days ishlatish kerak edi
        break;
    case 'monthly':
        $repeatDays = $request->monthly_days;
        break;
    case 'daily':
        $repeatDays = null;
        break;
}

            // Event yaratish
            Event::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'colors' => $request->color,
                'repeat_type' => $request->repeat_type,
                'repeat_interval' => 1,
                'repeat_days_moth' => json_encode($repeatDays),
                'start_date' => json_encode($request->event_times),
                'end_date' => 1, // default bo'sh array
                'status' => 'active'
            ]);

            return redirect()->route('dashboard.events.index')
                           ->with('success', 'Event muvaffaqiyatli yaratildi!');


        }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

       try {
            $event = Event::where('id', $id)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();

            $eventTitle = $event->title;
            $event->delete();

            return redirect()->back()->with('success', "Event '{$eventTitle}' muvaffaqiyatli o'chirildi!");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Event topilmadi yoki sizga tegishli emas!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Event o\'chirishda xatolik yuz berdi!');
        }
    }


    }

