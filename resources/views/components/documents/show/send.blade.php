<x-show.accordion type="send" :open="($accordionActive == 'send')">
    <x-slot name="head">
        <x-show.accordion.head
            title="{{ trans('general.send') }}"
            description="{!! trans($description, [
                'user' => $user_name,
                'type' => $type_lowercase,
                'date' => $last_sent_date,
            ]) !!}"
        />
    </x-slot>

    <x-slot name="body">
        <div class="flex flex-wrap space-x-3 rtl:space-x-reverse">
            @if (! $hideEmail)
                @if ($document->contact->has_email)
                    @if ($document->status != 'cancelled')
                        <x-button id="show-slider-actions-send-email-{{ $document->type }}" kind="secondary" @click="onSendEmail('{{ route($emailRoute, $document->id) }}')">
                            {{ trans($textEmail) }}
                        </x-button>
                    @else
                        <x-button kind="disabled" disabled="disabled">
                            {{ trans($textEmail) }}
                        </x-button>
                    @endif
                @else
                    <x-tooltip message="{{ trans('invoices.messages.email_required') }}" placement="top">
                        <x-dropdown.button disabled="disabled">
                            {{ trans($textEmail) }}
                        </x-dropdown.button>
                    </x-tooltip>
                @endif
            @endif

            @if (! $hideMarkSent)
                @can($permissionUpdate)
                    @if ($document->status == 'draft')
                        <x-link id="show-slider-actions-mark-sent-{{ $document->type }}" href="{{ route($markSentRoute, $document->id) }}" @click="e => e.target.classList.add('disabled')">
                            <x-link.loading>
                                {{ trans($textMarkSent) }}
                            </x-link.loading>
                        </x-link>
                    @else
                        <x-button kind="disabled" disabled="disabled">
                            {{ trans($textMarkSent) }}
                        </x-button>
                    @endif
                @endcan
            @endif

            @if (! $hideShare)
                @if ($document->status != 'cancelled')
                    <x-button id="show-slider-actions-share-link-{{ $document->type }}" @click="onShareLink('{{ route($shareRoute, $document->id) }}')">
                        {{ trans('general.share_link') }}
                    </x-button>
                @endif
            @endif

            @if ($histories->count())
                <div class="text-xs mt-6" style="margin-left: 0 !important;">
                    <span class="font-medium">
                        {{ trans_choice('general.histories', 1) }}:
                    </span>

                    @foreach ($histories as $history)
                        <div class="my-4">
                            <span>
                                {{ trans('documents.slider.send', [
                                    'user' => $history->owner->name,
                                    'type' => $type_lowercase,
                                    'date' => company_date($history->created_at),
                                ]) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-slot>
</x-show.accordion>
