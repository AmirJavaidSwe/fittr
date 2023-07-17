<div>
    <p>Your booking has been confirmed.</p>
    <p>Details</p>
    <p>Class Title: {{ $booking->class?->title }}</p>
    <p>Class Type: {{ $booking->class?->classType?->title }}</p>
    <p>Date: {{ $booking->class?->start_date->format($settings['date_format']->format_string) }}</p>
    <p>Start: {{ $booking->class?->start_date->format($settings['time_format']->format_string) }}</p>
    <p>End: {{ $booking->class?->end_date->format($settings['time_format']->format_string) }}</p>
    <p>Duration: {{ $booking->class?->duration }}</p>
    <p>Instructor: {{ $booking->class?->instructor?->name }}</p>
    <p>Location: {{ $booking->class?->studio?->location?->title }}</p>
</div>