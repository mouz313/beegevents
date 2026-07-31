<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:-apple-system,sans-serif;background:#FBF6EC;padding:24px;">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:32px;border:1px solid #E8E2D5;">
    <div style="text-align:center;margin-bottom:24px;">
        <div style="width:48px;height:48px;background:#D4A017;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#2B2620;">B</div>
        <h2 style="color:#2B2620;margin:12px 0 4px;">BeeG Events</h2>
        <p style="color:#6B6660;font-size:13px;margin:0;">
            @switch($recipientType)
                @case('vendor')
                    New Inquiry for Your Listing
                @break
                @case('admin')
                    New Customer Inquiry
                @break
                @default
                    Inquiry Confirmation
            @endswitch
        </p>
    </div>

    <hr style="border:none;border-top:1px solid #E8E2D5;margin:16px 0;">

    <table style="width:100%;font-size:13px;color:#2B2620;">
        <tr><td style="padding:6px 0;color:#6B6660;width:100px;">Name</td><td style="padding:6px 0;"><strong>{{ $inquiry->name }}</strong></td></tr>
        <tr><td style="padding:6px 0;color:#6B6660;">Email</td><td style="padding:6px 0;"><strong>{{ $inquiry->email }}</strong></td></tr>
        @if($inquiry->phone)
        <tr><td style="padding:6px 0;color:#6B6660;">Phone</td><td style="padding:6px 0;"><strong>{{ $inquiry->phone }}</strong></td></tr>
        @endif
        <tr><td style="padding:6px 0;color:#6B6660;">Vendor</td><td style="padding:6px 0;"><strong>{{ $inquiry->vendorProfile->business_name ?? 'N/A' }}</strong></td></tr>
        <tr><td style="padding:6px 0;color:#6B6660;">Listing</td><td style="padding:6px 0;">
            @php $item = $inquiry->inquiriable; @endphp
            <strong>{{ $item ? $item->name ?? $item->title ?? 'N/A' : 'N/A' }}</strong>
        </td></tr>
    </table>

    <hr style="border:none;border-top:1px solid #E8E2D5;margin:16px 0;">

    <h4 style="color:#2B2620;font-size:14px;margin:0 0 8px;">Message</h4>
    <p style="color:#2B2620;font-size:13px;line-height:1.6;background:#FBF6EC;padding:14px;border-radius:8px;margin:0;">{{ $inquiry->message }}</p>

    <hr style="border:none;border-top:1px solid #E8E2D5;margin:16px 0;">

    <p style="font-size:12px;color:#6B6660;text-align:center;margin:0;">
        Sent via BeeG Events — Pakistan's trusted event planning platform
    </p>
</div>
</body>
</html>
