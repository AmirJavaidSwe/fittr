<td class="header">
    <table align="center" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td width="150">
                <a href="{{ route('ss.home', ['subdomain' => $business_settings['subdomain']]) }}" style="display: inline-block;" target="_blank" rel="noopener">
                    @if(@$business_settings['logo'])
                    <img width="150" src="{{ config('app.asset_public_url') . $business_settings['logo'] }}"
                        alt="{{ $business_settings['business_name'] }}">
                    @else
                    <p class="logo-text">{{ $business_settings['business_name'] }}</p>
                    @endif
                </a>
            </td>
        </tr>
    </table>
</td>