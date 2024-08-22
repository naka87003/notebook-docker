<x-mail::message>
# You have a new follower!

{{ $follower->name }} followed you.

<img src="{{ $message->embed(storage_path('app/public/' . $follower->image_path)) }}">

<x-mail::button :url="config('app.url') . '/timeline?user='. $follower->id">
Check
</x-mail::button>
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
