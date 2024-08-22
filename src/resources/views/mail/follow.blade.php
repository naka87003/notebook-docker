<x-mail::message>
# You have a new follower!

{{ $follower->name }} followed you.

@if ($follower->image_path)
  <img src="{{ $message->embed(storage_path('app/public/' . $follower->image_path)) }}">
  <br>
  <br>
@endif
<p>Please click the button below to check your new follower's posts.</p>

<x-mail::button :url="config('app.url') . '/timeline?user='. $follower->id">
Check
</x-mail::button>
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
