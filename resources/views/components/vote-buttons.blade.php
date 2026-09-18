@props([
    'type', // 'post' lub 'comment'
    'id', // ID treści
    'score' => 0, // Score (upvotes - downvotes)
    'userVote' => null, // 'upvote', 'downvote' lub null
    'size' => 'md', // 'sm', 'md', 'lg'
    'layout' => 'horizontal', // 'horizontal' lub 'vertical'
])

@php
    $auth = auth()->check();
    $sizeClasses = match ($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => '',
    };
    $iconSize = match ($size) {
        'sm' => 'fs-6',
        'lg' => 'fs-3',
        default => 'fs-5',
    };
    $layoutClass = $layout === 'vertical' ? 'flex-column gap-1' : 'flex-row gap-2 align-items-center';
@endphp

<div class="vote-buttons d-flex {{ $layoutClass }}">
    {{-- Upvote --}}
    <form method="POST" action="{{ route('vote.up', ['type' => $type, 'id' => $id]) }}" class="d-inline">
        @csrf
        <button type="submit"
            class="btn {{ $sizeClasses }} {{ $userVote === 'upvote' ? 'btn-success' : 'btn-outline-success' }}"
            @if (!$auth) disabled title="Zaloguj się, aby zagłosować" @endif title="Głosuj za">
            <i class="bi bi-hand-thumbs-up{{ $userVote === 'upvote' ? '-fill' : '' }} {{ $iconSize }}"></i>
        </button>
    </form>

    {{-- Score --}}
    <span class="fw-bold {{ $score > 0 ? 'text-success' : ($score < 0 ? 'text-danger' : 'text-muted') }}"
        style="min-width: 2rem; text-align: center;">
        {{ $score > 0 ? '+' : '' }}{{ $score }}
    </span>

    {{-- Downvote --}}
    <form method="POST" action="{{ route('vote.down', ['type' => $type, 'id' => $id]) }}" class="d-inline">
        @csrf
        <button type="submit"
            class="btn {{ $sizeClasses }} {{ $userVote === 'downvote' ? 'btn-danger' : 'btn-outline-danger' }}"
            @if (!$auth) disabled title="Zaloguj się, aby zagłosować" @endif
            title="Głosuj przeciw">
            <i class="bi bi-hand-thumbs-down{{ $userVote === 'downvote' ? '-fill' : '' }} {{ $iconSize }}"></i>
        </button>
    </form>
</div>
