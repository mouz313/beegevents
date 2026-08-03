@if($profile && $profile->isFeatured())
    <span class="badge-feature {{ $profile->feature_tier }}">
        <i class="ti ti-{{ $profile->feature_tier === 'premium' ? 'crown' : 'star' }}"></i> {{ ucfirst($profile->feature_tier) }}
    </span>
@endif
