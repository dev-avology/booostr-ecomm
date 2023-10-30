<style>
    .row {
        /* height: 79px;
        width: 572px;
        margin-left: 266px;
        margin-top: 43px;
        border-radius: 10px 10px 0 0; */
        background-color: rgb(0, 192, 255);
        /* display: inline-flex; */
        }
</style>
{{-- <div class="row">
    @if (!empty(tenant()->logo))
        <img src="{{ env('WP_URL') }}{{ tenant()->logo }}"
        height="80" width="160" />
    @endif

    <p>Login</p>
</div> --}}

<tr class="row">
    <td class="header">
        <table width="50%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <a href="" style="display: inline-block;">
                        @if (!empty(tenant()->logo))
                            <img src="{{ env('WP_URL') }}{{ tenant()->logo }}" height="80" width="160" />
                        @endif
                    </a>
                </td>
            </tr>
        </table>
    </td>
    <td>Logo</td>
</tr>
