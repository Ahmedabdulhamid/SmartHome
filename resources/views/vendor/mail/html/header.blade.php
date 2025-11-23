@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp

<tr>
    <td class="header">
        <a href="{{ $url ?? url('/') }}" style="display: inline-block;">


                <img src="{{ Storage::url($setting->site_logo) }}" class="logo" >


        </a>
    </td>
</tr>
