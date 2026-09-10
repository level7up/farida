<x-mail::message>
# You've Been Invited

You have been invited to join **{{ $invitation->organization->name }}** as a **{{ $invitation->role }}**.

Click the button below to accept this invitation:

<x-mail::button :url="url('/invitations/accept/' . $invitation->token)">
Accept Invitation
</x-mail::button>

This invitation will expire in 7 days.

If you did not expect this invitation, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
