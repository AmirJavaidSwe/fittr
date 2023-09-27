<td>
    <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <p>{{ $business_settings['business_name'] }}</p>
                <p>You are receiving this email because you opted in via our website.</p>
            </td>
        </tr>
        @if(
            @$business_settings['link_facebook']
            || @$business_settings['link_instagram']
            || @$business_settings['link_twitter']
            || @$business_settings['link_dribble']
            || @$business_settings['link_youtube']
        )
        <tr>
            <td align="center">
                <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        @if(@$business_settings['link_facebook'])
                        <td>
                            <a href="{{ $business_settings['link_facebook'] }}" target="_blank" rel="noopener">
                                <img src="{{ asset('images/social_icons/facebook.png') }}" class="social-icon" alt="facebook" />
                            </a>
                        </td>
                        @endif

                        @if(@$business_settings['link_instagram'])
                        <td>
                            <a href="{{ $business_settings['link_instagram'] }}" target="_blank" rel="noopener">
                                <img src="{{ asset('images/social_icons/instagram.png') }}" class="social-icon" alt="instagram" />
                            </a>
                        </td>
                        @endif

                        @if(@$business_settings['link_twitter'])
                        <td>
                            <a href="{{ $business_settings['link_twitter'] }}" target="_blank" rel="noopener">
                                <img src="{{ asset('images/social_icons/twitter.png') }}" class="social-icon" alt="twitter" />
                            </a>
                        </td>
                        @endif

                        @if(@$business_settings['link_dribble'])
                        <td>
                            <a href="{{ $business_settings['link_dribble'] }}" target="_blank" rel="noopener">
                                <img src="{{ asset('images/social_icons/dribble.png') }}" class="social-icon" alt="dribble" />
                            </a>
                        </td>
                        @endif

                        @if(@$business_settings['link_youtube'])
                        <td>
                            <a href="{{ $business_settings['link_youtube'] }}" target="_blank" rel="noopener">
                                <img src="{{ asset('images/social_icons/youtube.png') }}" class="social-icon" alt="youtube" />
                            </a>
                        </td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
        @endif
        <tr>
            <td align="center">
                @if($business_settings['address_line1'])
                <p>{{ $business_settings['address_line1'] }}</p>
                @endif

                @if($business_settings['city'])
                <p>{{ $business_settings['city'] }}</p>
                @endif

                @if($template['unsubscribe'])
                <a href="#" target="_blank" rel="noopener">Unsubsrcibe</a>
                @endif
            </td>
        </tr>
        <tr>
            <td align="center">
                <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td width="100">
                            <a href="{{ route('ss.home', ['subdomain' => $business_settings['subdomain']]) }}" style="display: inline-block;" target="_blank" rel="noopener">
                                @if(@$business_settings['logo'])
                                <img width="100" src="{{ config('app.asset_public_url') . $business_settings['logo'] }}"
                                    alt="{{ $business_settings['business_name'] }}">
                                @else
                                <p class="logo-text-small">{{ $business_settings['business_name'] }}</p>
                                @endif
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</td>