@extends('layouts.backend.app')

@section('title','Dashboard')

@section('content')
<section class="section">
{{-- section title --}}
<div class="section-header">
 <a href="{{ route('seller.site-settings.index') }}" class="btn btn-primary mr-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>{{ __('Store details') }}</h1>
</div>
{{-- /section title --}}
<div class="row">
   <div class="col-lg-12">
      <form class="ajaxform" method="post" action="{{ route('seller.site-settings.update','general') }}">
                @csrf
                @method('PUT')
         <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store contact information') }}</h6>
                <strong>{{ __('Your customers will use this information to contact you.') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store name :') }}  </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ $store_name }}"  required="" name="store_name" class="form-control" max="30">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Sender email') }} : </label>
                        <div class="col-lg-12">
                            <input type="email" disabled value="{{ $store_sender_email ?? '' }}" required  name="store_sender_email" class="form-control"  max="50">
                            <small>{{ __('Your customers will see this address if you email them.') }}</small>
                        </div>
                    </div>
                    {{-- <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Latitude:') }}  </label>
                        <div class="col-lg-12">
                            <input type="number" disabled value="{{ $lat_lang[0] }}"  step="any"  required="" name="latitude" class="form-control"  placeholder="31.9686">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Longitude:') }}  </label>
                        <div class="col-lg-12">
                            <input type="number" disabled value="{{ $lat_lang[1] }}"  step="any"  required="" name="longitude" class="form-control"  placeholder="99.9018">
                        </div>
                    </div> --}}
                   <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Time zone') }} : </label>
                        <div class="col-lg-12">
                                <select disabled class="form-control selectric" name="timezone" id="timezone" >
                                    <option value='Africa/Abidjan'>Africa/Abidjan</option>
                                    <option value='Africa/Accra'>Africa/Accra</option>
                                    <option value='Africa/Addis_Ababa'>Africa/Addis_Ababa</option>
                                    <option value='Africa/Algiers'>Africa/Algiers</option>
                                    <option value='Africa/Asmara'>Africa/Asmara</option>
                                    <option value='Africa/Bamako'>Africa/Bamako</option>
                                    <option value='Africa/Bangui'>Africa/Bangui</option>
                                    <option value='Africa/Banjul'>Africa/Banjul</option>
                                    <option value='Africa/Bissau'>Africa/Bissau</option>
                                    <option value='Africa/Blantyre'>Africa/Blantyre</option>
                                    <option value='Africa/Brazzaville'>Africa/Brazzaville</option>
                                    <option value='Africa/Bujumbura'>Africa/Bujumbura</option>
                                    <option value='Africa/Cairo'>Africa/Cairo</option>
                                    <option value='Africa/Casablanca'>Africa/Casablanca</option>
                                    <option value='Africa/Ceuta'>Africa/Ceuta</option>
                                    <option value='Africa/Conakry'>Africa/Conakry</option>
                                    <option value='Africa/Dakar'>Africa/Dakar</option>
                                    <option value='Africa/Dar_es_Salaam'>Africa/Dar_es_Salaam</option>
                                    <option value='Africa/Djibouti'>Africa/Djibouti</option>
                                    <option value='Africa/Douala'>Africa/Douala</option>
                                    <option value='Africa/El_Aaiun'>Africa/El_Aaiun</option>
                                    <option value='Africa/Freetown'>Africa/Freetown</option>
                                    <option value='Africa/Gaborone'>Africa/Gaborone</option>
                                    <option value='Africa/Harare'>Africa/Harare</option>
                                    <option value='Africa/Johannesburg'>Africa/Johannesburg</option>
                                    <option value='Africa/Juba'>Africa/Juba</option>
                                    <option value='Africa/Kampala'>Africa/Kampala</option>
                                    <option value='Africa/Khartoum'>Africa/Khartoum</option>
                                    <option value='Africa/Kigali'>Africa/Kigali</option>
                                    <option value='Africa/Kinshasa'>Africa/Kinshasa</option>
                                    <option value='Africa/Lagos'>Africa/Lagos</option>
                                    <option value='Africa/Libreville'>Africa/Libreville</option>
                                    <option value='Africa/Lome'>Africa/Lome</option>
                                    <option value='Africa/Luanda'>Africa/Luanda</option>
                                    <option value='Africa/Lubumbashi'>Africa/Lubumbashi</option>
                                    <option value='Africa/Lusaka'>Africa/Lusaka</option>
                                    <option value='Africa/Malabo'>Africa/Malabo</option>
                                    <option value='Africa/Maputo'>Africa/Maputo</option>
                                    <option value='Africa/Maseru'>Africa/Maseru</option>
                                    <option value='Africa/Mbabane'>Africa/Mbabane</option>
                                    <option value='Africa/Mogadishu'>Africa/Mogadishu</option>
                                    <option value='Africa/Monrovia'>Africa/Monrovia</option>
                                    <option value='Africa/Nairobi'>Africa/Nairobi</option>
                                    <option value='Africa/Ndjamena'>Africa/Ndjamena</option>
                                    <option value='Africa/Niamey'>Africa/Niamey</option>
                                    <option value='Africa/Nouakchott'>Africa/Nouakchott</option>
                                    <option value='Africa/Ouagadougou'>Africa/Ouagadougou</option>
                                    <option value='Africa/Porto-Novo'>Africa/Porto-Novo</option>
                                    <option value='Africa/Sao_Tome'>Africa/Sao_Tome</option>
                                    <option value='Africa/Tripoli'>Africa/Tripoli</option>
                                    <option value='Africa/Tunis'>Africa/Tunis</option>
                                    <option value='Africa/Windhoek'>Africa/Windhoek</option>
                                    <option value='America/Adak'>America/Adak</option>
                                    <option value='America/Anchorage'>America/Anchorage</option>
                                    <option value='America/Anguilla'>America/Anguilla</option>
                                    <option value='America/Antigua'>America/Antigua</option>
                                    <option value='America/Araguaina'>America/Araguaina</option>
                                    <option value='America/Argentina/Buenos_Aires'>America/Argentina/Buenos_Aires</option>
                                    <option value='America/Argentina/Catamarca'>America/Argentina/Catamarca</option>
                                    <option value='America/Argentina/Cordoba'>America/Argentina/Cordoba</option>
                                    <option value='America/Argentina/Jujuy'>America/Argentina/Jujuy</option>
                                    <option value='America/Argentina/La_Rioja'>America/Argentina/La_Rioja</option>
                                    <option value='America/Argentina/Mendoza'>America/Argentina/Mendoza</option>
                                    <option value='America/Argentina/Rio_Gallegos'>America/Argentina/Rio_Gallegos</option>
                                    <option value='America/Argentina/Salta'>America/Argentina/Salta</option>
                                    <option value='America/Argentina/San_Juan'>America/Argentina/San_Juan</option>
                                    <option value='America/Argentina/San_Luis'>America/Argentina/San_Luis</option>
                                    <option value='America/Argentina/Tucuman'>America/Argentina/Tucuman</option>
                                    <option value='America/Argentina/Ushuaia'>America/Argentina/Ushuaia</option>
                                    <option value='America/Aruba'>America/Aruba</option>
                                    <option value='America/Asuncion'>America/Asuncion</option>
                                    <option value='America/Atikokan'>America/Atikokan</option>
                                    <option value='America/Bahia'>America/Bahia</option>
                                    <option value='America/Bahia_Banderas'>America/Bahia_Banderas</option>
                                    <option value='America/Barbados'>America/Barbados</option>
                                    <option value='America/Belem'>America/Belem</option>
                                    <option value='America/Belize'>America/Belize</option>
                                    <option value='America/Blanc-Sablon'>America/Blanc-Sablon</option>
                                    <option value='America/Boa_Vista'>America/Boa_Vista</option>
                                    <option value='America/Bogota'>America/Bogota</option>
                                    <option value='America/Boise'>America/Boise</option>
                                    <option value='America/Cambridge_Bay'>America/Cambridge_Bay</option>
                                    <option value='America/Campo_Grande'>America/Campo_Grande</option>
                                    <option value='America/Cancun'>America/Cancun</option>
                                    <option value='America/Caracas'>America/Caracas</option>
                                    <option value='America/Cayenne'>America/Cayenne</option>
                                    <option value='America/Cayman'>America/Cayman</option>
                                    <option value='America/Chicago'>America/Chicago</option>
                                    <option value='America/Chihuahua'>America/Chihuahua</option>
                                    <option value='America/Costa_Rica'>America/Costa_Rica</option>
                                    <option value='America/Creston'>America/Creston</option>
                                    <option value='America/Cuiaba'>America/Cuiaba</option>
                                    <option value='America/Curacao'>America/Curacao</option>
                                    <option value='America/Danmarkshavn'>America/Danmarkshavn</option>
                                    <option value='America/Dawson'>America/Dawson</option>
                                    <option value='America/Dawson_Creek'>America/Dawson_Creek</option>
                                    <option value='America/Denver'>America/Denver</option>
                                    <option value='America/Detroit'>America/Detroit</option>
                                    <option value='America/Dominica'>America/Dominica</option>
                                    <option value='America/Edmonton'>America/Edmonton</option>
                                    <option value='America/Eirunepe'>America/Eirunepe</option>
                                    <option value='America/El_Salvador'>America/El_Salvador</option>
                                    <option value='America/Fort_Nelson'>America/Fort_Nelson</option>
                                    <option value='America/Fortaleza'>America/Fortaleza</option>
                                    <option value='America/Glace_Bay'>America/Glace_Bay</option>
                                    <option value='America/Godthab'>America/Godthab</option>
                                    <option value='America/Goose_Bay'>America/Goose_Bay</option>
                                    <option value='America/Grand_Turk'>America/Grand_Turk</option>
                                    <option value='America/Grenada'>America/Grenada</option>
                                    <option value='America/Guadeloupe'>America/Guadeloupe</option>
                                    <option value='America/Guatemala'>America/Guatemala</option>
                                    <option value='America/Guayaquil'>America/Guayaquil</option>
                                    <option value='America/Guyana'>America/Guyana</option>
                                    <option value='America/Halifax'>America/Halifax</option>
                                    <option value='America/Havana'>America/Havana</option>
                                    <option value='America/Hermosillo'>America/Hermosillo</option>
                                    <option value='America/Indiana/Indianapolis'>America/Indiana/Indianapolis</option>
                                    <option value='America/Indiana/Knox'>America/Indiana/Knox</option>
                                    <option value='America/Indiana/Marengo'>America/Indiana/Marengo</option>
                                    <option value='America/Indiana/Petersburg'>America/Indiana/Petersburg</option>
                                    <option value='America/Indiana/Tell_City'>America/Indiana/Tell_City</option>
                                    <option value='America/Indiana/Vevay'>America/Indiana/Vevay</option>
                                    <option value='America/Indiana/Vincennes'>America/Indiana/Vincennes</option>
                                    <option value='America/Indiana/Winamac'>America/Indiana/Winamac</option>
                                    <option value='America/Inuvik'>America/Inuvik</option>
                                    <option value='America/Iqaluit'>America/Iqaluit</option>
                                    <option value='America/Jamaica'>America/Jamaica</option>
                                    <option value='America/Juneau'>America/Juneau</option>
                                    <option value='America/Kentucky/Louisville'>America/Kentucky/Louisville</option>
                                    <option value='America/Kentucky/Monticello'>America/Kentucky/Monticello</option>
                                    <option value='America/Kralendijk'>America/Kralendijk</option>
                                    <option value='America/La_Paz'>America/La_Paz</option>
                                    <option value='America/Lima'>America/Lima</option>
                                    <option value='America/Los_Angeles'>America/Los_Angeles</option>
                                    <option value='America/Lower_Princes'>America/Lower_Princes</option>
                                    <option value='America/Maceio'>America/Maceio</option>
                                    <option value='America/Managua'>America/Managua</option>
                                    <option value='America/Manaus'>America/Manaus</option>
                                    <option value='America/Marigot'>America/Marigot</option>
                                    <option value='America/Martinique'>America/Martinique</option>
                                    <option value='America/Matamoros'>America/Matamoros</option>
                                    <option value='America/Mazatlan'>America/Mazatlan</option>
                                    <option value='America/Menominee'>America/Menominee</option>
                                    <option value='America/Merida'>America/Merida</option>
                                    <option value='America/Metlakatla'>America/Metlakatla</option>
                                    <option value='America/Mexico_City'>America/Mexico_City</option>
                                    <option value='America/Miquelon'>America/Miquelon</option>
                                    <option value='America/Moncton'>America/Moncton</option>
                                    <option value='America/Monterrey'>America/Monterrey</option>
                                    <option value='America/Montevideo'>America/Montevideo</option>
                                    <option value='America/Montserrat'>America/Montserrat</option>
                                    <option value='America/Nassau'>America/Nassau</option>
                                    <option value='America/New_York'>America/New_York</option>
                                    <option value='America/Nipigon'>America/Nipigon</option>
                                    <option value='America/Nome'>America/Nome</option>
                                    <option value='America/Noronha'>America/Noronha</option>
                                    <option value='America/North_Dakota/Beulah'>America/North_Dakota/Beulah</option>
                                    <option value='America/North_Dakota/Center'>America/North_Dakota/Center</option>
                                    <option value='America/North_Dakota/New_Salem'>America/North_Dakota/New_Salem</option>
                                    <option value='America/Ojinaga'>America/Ojinaga</option>
                                    <option value='America/Panama'>America/Panama</option>
                                    <option value='America/Pangnirtung'>America/Pangnirtung</option>
                                    <option value='America/Paramaribo'>America/Paramaribo</option>
                                    <option value='America/Phoenix'>America/Phoenix</option>
                                    <option value='America/Port-au-Prince'>America/Port-au-Prince</option>
                                    <option value='America/Port_of_Spain'>America/Port_of_Spain</option>
                                    <option value='America/Porto_Velho'>America/Porto_Velho</option>
                                    <option value='America/Puerto_Rico'>America/Puerto_Rico</option>
                                    <option value='America/Punta_Arenas'>America/Punta_Arenas</option>
                                    <option value='America/Rainy_River'>America/Rainy_River</option>
                                    <option value='America/Rankin_Inlet'>America/Rankin_Inlet</option>
                                    <option value='America/Recife'>America/Recife</option>
                                    <option value='America/Regina'>America/Regina</option>
                                    <option value='America/Resolute'>America/Resolute</option>
                                    <option value='America/Rio_Branco'>America/Rio_Branco</option>
                                    <option value='America/Santarem'>America/Santarem</option>
                                    <option value='America/Santiago'>America/Santiago</option>
                                    <option value='America/Santo_Domingo'>America/Santo_Domingo</option>
                                    <option value='America/Sao_Paulo'>America/Sao_Paulo</option>
                                    <option value='America/Scoresbysund'>America/Scoresbysund</option>
                                    <option value='America/Sitka'>America/Sitka</option>
                                    <option value='America/St_Barthelemy'>America/St_Barthelemy</option>
                                    <option value='America/St_Johns'>America/St_Johns</option>
                                    <option value='America/St_Kitts'>America/St_Kitts</option>
                                    <option value='America/St_Lucia'>America/St_Lucia</option>
                                    <option value='America/St_Thomas'>America/St_Thomas</option>
                                    <option value='America/St_Vincent'>America/St_Vincent</option>
                                    <option value='America/Swift_Current'>America/Swift_Current</option>
                                    <option value='America/Tegucigalpa'>America/Tegucigalpa</option>
                                    <option value='America/Thule'>America/Thule</option>
                                    <option value='America/Thunder_Bay'>America/Thunder_Bay</option>
                                    <option value='America/Tijuana'>America/Tijuana</option>
                                    <option value='America/Toronto'>America/Toronto</option>
                                    <option value='America/Tortola'>America/Tortola</option>
                                    <option value='America/Vancouver'>America/Vancouver</option>
                                    <option value='America/Whitehorse'>America/Whitehorse</option>
                                    <option value='America/Winnipeg'>America/Winnipeg</option>
                                    <option value='America/Yakutat'>America/Yakutat</option>
                                    <option value='America/Yellowknife'>America/Yellowknife</option>
                                    <option value='Antarctica/Casey'>Antarctica/Casey</option>
                                    <option value='Antarctica/Davis'>Antarctica/Davis</option>
                                    <option value='Antarctica/DumontDUrville'>Antarctica/DumontDUrville</option>
                                    <option value='Antarctica/Macquarie'>Antarctica/Macquarie</option>
                                    <option value='Antarctica/Mawson'>Antarctica/Mawson</option>
                                    <option value='Antarctica/McMurdo'>Antarctica/McMurdo</option>
                                    <option value='Antarctica/Palmer'>Antarctica/Palmer</option>
                                    <option value='Antarctica/Rothera'>Antarctica/Rothera</option>
                                    <option value='Antarctica/Syowa'>Antarctica/Syowa</option>
                                    <option value='Antarctica/Troll'>Antarctica/Troll</option>
                                    <option value='Antarctica/Vostok'>Antarctica/Vostok</option>
                                    <option value='Arctic/Longyearbyen'>Arctic/Longyearbyen</option>
                                    <option value='Asia/Aden'>Asia/Aden</option>
                                    <option value='Asia/Almaty'>Asia/Almaty</option>
                                    <option value='Asia/Amman'>Asia/Amman</option>
                                    <option value='Asia/Anadyr'>Asia/Anadyr</option>
                                    <option value='Asia/Aqtau'>Asia/Aqtau</option>
                                    <option value='Asia/Aqtobe'>Asia/Aqtobe</option>
                                    <option value='Asia/Ashgabat'>Asia/Ashgabat</option>
                                    <option value='Asia/Atyrau'>Asia/Atyrau</option>
                                    <option value='Asia/Baghdad'>Asia/Baghdad</option>
                                    <option value='Asia/Bahrain'>Asia/Bahrain</option>
                                    <option value='Asia/Baku'>Asia/Baku</option>
                                    <option value='Asia/Bangkok'>Asia/Bangkok</option>
                                    <option value='Asia/Barnaul'>Asia/Barnaul</option>
                                    <option value='Asia/Beirut'>Asia/Beirut</option>
                                    <option value='Asia/Bishkek'>Asia/Bishkek</option>
                                    <option value='Asia/Brunei'>Asia/Brunei</option>
                                    <option value='Asia/Chita'>Asia/Chita</option>
                                    <option value='Asia/Choibalsan'>Asia/Choibalsan</option>
                                    <option value='Asia/Colombo'>Asia/Colombo</option>
                                    <option value='Asia/Damascus'>Asia/Damascus</option>
                                    <option value='Asia/Dhaka'>Asia/Dhaka</option>
                                    <option value='Asia/Dili'>Asia/Dili</option>
                                    <option value='Asia/Dubai'>Asia/Dubai</option>
                                    <option value='Asia/Dushanbe'>Asia/Dushanbe</option>
                                    <option value='Asia/Famagusta'>Asia/Famagusta</option>
                                    <option value='Asia/Gaza'>Asia/Gaza</option>
                                    <option value='Asia/Hebron'>Asia/Hebron</option>
                                    <option value='Asia/Ho_Chi_Minh'>Asia/Ho_Chi_Minh</option>
                                    <option value='Asia/Hong_Kong'>Asia/Hong_Kong</option>
                                    <option value='Asia/Hovd'>Asia/Hovd</option>
                                    <option value='Asia/Irkutsk'>Asia/Irkutsk</option>
                                    <option value='Asia/Jakarta'>Asia/Jakarta</option>
                                    <option value='Asia/Jayapura'>Asia/Jayapura</option>
                                    <option value='Asia/Jerusalem'>Asia/Jerusalem</option>
                                    <option value='Asia/Kabul'>Asia/Kabul</option>
                                    <option value='Asia/Kamchatka'>Asia/Kamchatka</option>
                                    <option value='Asia/Karachi'>Asia/Karachi</option>
                                    <option value='Asia/Kathmandu'>Asia/Kathmandu</option>
                                    <option value='Asia/Khandyga'>Asia/Khandyga</option>
                                    <option value='Asia/Kolkata'>Asia/Kolkata</option>
                                    <option value='Asia/Krasnoyarsk'>Asia/Krasnoyarsk</option>
                                    <option value='Asia/Kuala_Lumpur'>Asia/Kuala_Lumpur</option>
                                    <option value='Asia/Kuching'>Asia/Kuching</option>
                                    <option value='Asia/Kuwait'>Asia/Kuwait</option>
                                    <option value='Asia/Macau'>Asia/Macau</option>
                                    <option value='Asia/Magadan'>Asia/Magadan</option>
                                    <option value='Asia/Makassar'>Asia/Makassar</option>
                                    <option value='Asia/Manila'>Asia/Manila</option>
                                    <option value='Asia/Muscat'>Asia/Muscat</option>
                                    <option value='Asia/Nicosia'>Asia/Nicosia</option>
                                    <option value='Asia/Novokuznetsk'>Asia/Novokuznetsk</option>
                                    <option value='Asia/Novosibirsk'>Asia/Novosibirsk</option>
                                    <option value='Asia/Omsk'>Asia/Omsk</option>
                                    <option value='Asia/Oral'>Asia/Oral</option>
                                    <option value='Asia/Phnom_Penh'>Asia/Phnom_Penh</option>
                                    <option value='Asia/Pontianak'>Asia/Pontianak</option>
                                    <option value='Asia/Pyongyang'>Asia/Pyongyang</option>
                                    <option value='Asia/Qatar'>Asia/Qatar</option>
                                    <option value='Asia/Qostanay'>Asia/Qostanay</option>
                                    <option value='Asia/Qyzylorda'>Asia/Qyzylorda</option>
                                    <option value='Asia/Riyadh'>Asia/Riyadh</option>
                                    <option value='Asia/Sakhalin'>Asia/Sakhalin</option>
                                    <option value='Asia/Samarkand'>Asia/Samarkand</option>
                                    <option value='Asia/Seoul'>Asia/Seoul</option>
                                    <option value='Asia/Shanghai'>Asia/Shanghai</option>
                                    <option value='Asia/Singapore'>Asia/Singapore</option>
                                    <option value='Asia/Srednekolymsk'>Asia/Srednekolymsk</option>
                                    <option value='Asia/Taipei'>Asia/Taipei</option>
                                    <option value='Asia/Tashkent'>Asia/Tashkent</option>
                                    <option value='Asia/Tbilisi'>Asia/Tbilisi</option>
                                    <option value='Asia/Tehran'>Asia/Tehran</option>
                                    <option value='Asia/Thimphu'>Asia/Thimphu</option>
                                    <option value='Asia/Tokyo'>Asia/Tokyo</option>
                                    <option value='Asia/Tomsk'>Asia/Tomsk</option>
                                    <option value='Asia/Ulaanbaatar'>Asia/Ulaanbaatar</option>
                                    <option value='Asia/Urumqi'>Asia/Urumqi</option>
                                    <option value='Asia/Ust-Nera'>Asia/Ust-Nera</option>
                                    <option value='Asia/Vientiane'>Asia/Vientiane</option>
                                    <option value='Asia/Vladivostok'>Asia/Vladivostok</option>
                                    <option value='Asia/Yakutsk'>Asia/Yakutsk</option>
                                    <option value='Asia/Yangon'>Asia/Yangon</option>
                                    <option value='Asia/Yekaterinburg'>Asia/Yekaterinburg</option>
                                    <option value='Asia/Yerevan'>Asia/Yerevan</option>
                                    <option value='Atlantic/Azores'>Atlantic/Azores</option>
                                    <option value='Atlantic/Bermuda'>Atlantic/Bermuda</option>
                                    <option value='Atlantic/Canary'>Atlantic/Canary</option>
                                    <option value='Atlantic/Cape_Verde'>Atlantic/Cape_Verde</option>
                                    <option value='Atlantic/Faroe'>Atlantic/Faroe</option>
                                    <option value='Atlantic/Madeira'>Atlantic/Madeira</option>
                                    <option value='Atlantic/Reykjavik'>Atlantic/Reykjavik</option>
                                    <option value='Atlantic/South_Georgia'>Atlantic/South_Georgia</option>
                                    <option value='Atlantic/St_Helena'>Atlantic/St_Helena</option>
                                    <option value='Atlantic/Stanley'>Atlantic/Stanley</option>
                                    <option value='Australia/Adelaide'>Australia/Adelaide</option>
                                    <option value='Australia/Brisbane'>Australia/Brisbane</option>
                                    <option value='Australia/Broken_Hill'>Australia/Broken_Hill</option>
                                    <option value='Australia/Currie'>Australia/Currie</option>
                                    <option value='Australia/Darwin'>Australia/Darwin</option>
                                    <option value='Australia/Eucla'>Australia/Eucla</option>
                                    <option value='Australia/Hobart'>Australia/Hobart</option>
                                    <option value='Australia/Lindeman'>Australia/Lindeman</option>
                                    <option value='Australia/Lord_Howe'>Australia/Lord_Howe</option>
                                    <option value='Australia/Melbourne'>Australia/Melbourne</option>
                                    <option value='Australia/Perth'>Australia/Perth</option>
                                    <option value='Australia/Sydney'>Australia/Sydney</option>
                                    <option value='Europe/Amsterdam'>Europe/Amsterdam</option>
                                    <option value='Europe/Andorra'>Europe/Andorra</option>
                                    <option value='Europe/Astrakhan'>Europe/Astrakhan</option>
                                    <option value='Europe/Athens'>Europe/Athens</option>
                                    <option value='Europe/Belgrade'>Europe/Belgrade</option>
                                    <option value='Europe/Berlin'>Europe/Berlin</option>
                                    <option value='Europe/Bratislava'>Europe/Bratislava</option>
                                    <option value='Europe/Brussels'>Europe/Brussels</option>
                                    <option value='Europe/Bucharest'>Europe/Bucharest</option>
                                    <option value='Europe/Budapest'>Europe/Budapest</option>
                                    <option value='Europe/Busingen'>Europe/Busingen</option>
                                    <option value='Europe/Chisinau'>Europe/Chisinau</option>
                                    <option value='Europe/Copenhagen'>Europe/Copenhagen</option>
                                    <option value='Europe/Dublin'>Europe/Dublin</option>
                                    <option value='Europe/Gibraltar'>Europe/Gibraltar</option>
                                    <option value='Europe/Guernsey'>Europe/Guernsey</option>
                                    <option value='Europe/Helsinki'>Europe/Helsinki</option>
                                    <option value='Europe/Isle_of_Man'>Europe/Isle_of_Man</option>
                                    <option value='Europe/Istanbul'>Europe/Istanbul</option>
                                    <option value='Europe/Jersey'>Europe/Jersey</option>
                                    <option value='Europe/Kaliningrad'>Europe/Kaliningrad</option>
                                    <option value='Europe/Kiev'>Europe/Kiev</option>
                                    <option value='Europe/Kirov'>Europe/Kirov</option>
                                    <option value='Europe/Lisbon'>Europe/Lisbon</option>
                                    <option value='Europe/Ljubljana'>Europe/Ljubljana</option>
                                    <option value='Europe/London'>Europe/London</option>
                                    <option value='Europe/Luxembourg'>Europe/Luxembourg</option>
                                    <option value='Europe/Madrid'>Europe/Madrid</option>
                                    <option value='Europe/Malta'>Europe/Malta</option>
                                    <option value='Europe/Mariehamn'>Europe/Mariehamn</option>
                                    <option value='Europe/Minsk'>Europe/Minsk</option>
                                    <option value='Europe/Monaco'>Europe/Monaco</option>
                                    <option value='Europe/Moscow'>Europe/Moscow</option>
                                    <option value='Europe/Oslo'>Europe/Oslo</option>
                                    <option value='Europe/Paris'>Europe/Paris</option>
                                    <option value='Europe/Podgorica'>Europe/Podgorica</option>
                                    <option value='Europe/Prague'>Europe/Prague</option>
                                    <option value='Europe/Riga'>Europe/Riga</option>
                                    <option value='Europe/Rome'>Europe/Rome</option>
                                    <option value='Europe/Samara'>Europe/Samara</option>
                                    <option value='Europe/San_Marino'>Europe/San_Marino</option>
                                    <option value='Europe/Sarajevo'>Europe/Sarajevo</option>
                                    <option value='Europe/Saratov'>Europe/Saratov</option>
                                    <option value='Europe/Simferopol'>Europe/Simferopol</option>
                                    <option value='Europe/Skopje'>Europe/Skopje</option>
                                    <option value='Europe/Sofia'>Europe/Sofia</option>
                                    <option value='Europe/Stockholm'>Europe/Stockholm</option>
                                    <option value='Europe/Tallinn'>Europe/Tallinn</option>
                                    <option value='Europe/Tirane'>Europe/Tirane</option>
                                    <option value='Europe/Ulyanovsk'>Europe/Ulyanovsk</option>
                                    <option value='Europe/Uzhgorod'>Europe/Uzhgorod</option>
                                    <option value='Europe/Vaduz'>Europe/Vaduz</option>
                                    <option value='Europe/Vatican'>Europe/Vatican</option>
                                    <option value='Europe/Vienna'>Europe/Vienna</option>
                                    <option value='Europe/Vilnius'>Europe/Vilnius</option>
                                    <option value='Europe/Volgograd'>Europe/Volgograd</option>
                                    <option value='Europe/Warsaw'>Europe/Warsaw</option>
                                    <option value='Europe/Zagreb'>Europe/Zagreb</option>
                                    <option value='Europe/Zaporozhye'>Europe/Zaporozhye</option>
                                    <option value='Europe/Zurich'>Europe/Zurich</option>
                                    <option value='Indian/Antananarivo'>Indian/Antananarivo</option>
                                    <option value='Indian/Chagos'>Indian/Chagos</option>
                                    <option value='Indian/Christmas'>Indian/Christmas</option>
                                    <option value='Indian/Cocos'>Indian/Cocos</option>
                                    <option value='Indian/Comoro'>Indian/Comoro</option>
                                    <option value='Indian/Kerguelen'>Indian/Kerguelen</option>
                                    <option value='Indian/Mahe'>Indian/Mahe</option>
                                    <option value='Indian/Maldives'>Indian/Maldives</option>
                                    <option value='Indian/Mauritius'>Indian/Mauritius</option>
                                    <option value='Indian/Mayotte'>Indian/Mayotte</option>
                                    <option value='Indian/Reunion'>Indian/Reunion</option>
                                    <option value='Pacific/Apia'>Pacific/Apia</option>
                                    <option value='Pacific/Auckland'>Pacific/Auckland</option>
                                    <option value='Pacific/Bougainville'>Pacific/Bougainville</option>
                                    <option value='Pacific/Chatham'>Pacific/Chatham</option>
                                    <option value='Pacific/Chuuk'>Pacific/Chuuk</option>
                                    <option value='Pacific/Easter'>Pacific/Easter</option>
                                    <option value='Pacific/Efate'>Pacific/Efate</option>
                                    <option value='Pacific/Enderbury'>Pacific/Enderbury</option>
                                    <option value='Pacific/Fakaofo'>Pacific/Fakaofo</option>
                                    <option value='Pacific/Fiji'>Pacific/Fiji</option>
                                    <option value='Pacific/Funafuti'>Pacific/Funafuti</option>
                                    <option value='Pacific/Galapagos'>Pacific/Galapagos</option>
                                    <option value='Pacific/Gambier'>Pacific/Gambier</option>
                                    <option value='Pacific/Guadalcanal'>Pacific/Guadalcanal</option>
                                    <option value='Pacific/Guam'>Pacific/Guam</option>
                                    <option value='Pacific/Honolulu'>Pacific/Honolulu</option>
                                    <option value='Pacific/Kiritimati'>Pacific/Kiritimati</option>
                                    <option value='Pacific/Kosrae'>Pacific/Kosrae</option>
                                    <option value='Pacific/Kwajalein'>Pacific/Kwajalein</option>
                                    <option value='Pacific/Majuro'>Pacific/Majuro</option>
                                    <option value='Pacific/Marquesas'>Pacific/Marquesas</option>
                                    <option value='Pacific/Midway'>Pacific/Midway</option>
                                    <option value='Pacific/Nauru'>Pacific/Nauru</option>
                                    <option value='Pacific/Niue'>Pacific/Niue</option>
                                    <option value='Pacific/Norfolk'>Pacific/Norfolk</option>
                                    <option value='Pacific/Noumea'>Pacific/Noumea</option>
                                    <option value='Pacific/Pago_Pago'>Pacific/Pago_Pago</option>
                                    <option value='Pacific/Palau'>Pacific/Palau</option>
                                    <option value='Pacific/Pitcairn'>Pacific/Pitcairn</option>
                                    <option value='Pacific/Pohnpei'>Pacific/Pohnpei</option>
                                    <option value='Pacific/Port_Moresby'>Pacific/Port_Moresby</option>
                                    <option value='Pacific/Rarotonga'>Pacific/Rarotonga</option>
                                    <option value='Pacific/Saipan'>Pacific/Saipan</option>
                                    <option value='Pacific/Tahiti'>Pacific/Tahiti</option>
                                    <option value='Pacific/Tarawa'>Pacific/Tarawa</option>
                                    <option value='Pacific/Tongatapu'>Pacific/Tongatapu</option>
                                    <option value='Pacific/Wake'>Pacific/Wake</option>
                                    <option value='Pacific/Wallis'>Pacific/Wallis</option>
                                    <option value='UTC'>UTC</option>
                            </select>
                        </div>
                    </div>
                    


                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>
         <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store Banner Images') }}</h6>
                <strong>{{ __('Your customers will see the banner images.') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Banner Image:') }} (Width: 875px & Height: 250px)</label>
                        <div class="col-lg-12">
                            <input type="file" name="banner" class="form-control" accept=".png,.jpeg,.jpg" >
                        </div>
                    </div>
                    {{-- <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Favicon:') }} (48x48)</label>
                        <div class="col-lg-12">
                            <input type="file"  name="favicon" class="form-control" accept=".ico" >
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Notification icon:') }} (50x50)</label>
                        <div class="col-lg-12">
                            <input type="file"  name="notification_icon" class="form-control" accept=".png" >
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Banner:') }}</label>
                        <div class="col-lg-12">
                            <input type="file"  name="banner" class="form-control" accept=".png" >
                        </div>
                    </div> --}}
                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>
         <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store address') }}</h6>
                <strong>{{ __('This address will appear on your invoices.') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Legal name of company :') }}  </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ $store_name }}" name="store_legal_name" class="form-control" max="50">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Phone') }} : </label>
                        <div class="col-lg-12">
                            <input type="number" disabled value="{{ str_replace('-','',$phone_number) ?? '' }}" name="store_legal_phone" class="form-control" required>
                        </div>
                    </div>
                     <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Email') }} : </label>
                        <div class="col-lg-12">
                            <input type="email" disabled value="{{ $store_sender_email ?? '' }}" name="store_legal_email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Address') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ isset($address[0]) ? $address[0] : '' }}"  name="store_legal_address" class="form-control" required>
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Apartment, suite, etc.') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ isset($address[1]) ? $address[1] : '' }}" name="store_legal_house" class="form-control" required>
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('City') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ trim($address[count($address)-2]) ?? '' }}" name="store_legal_city" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="from-group col-lg-6  mb-2">
                         <label for="" >{{ __('Country/region') }} : </label>
                          <input type="text" disabled value="{{ trim($address[count($address)-1]) ?? '' }}" name="country" class="form-control">
                        </div>
                        <div class="from-group col-lg-6  mb-2">
                         <label for="" >{{ __('Postal code') }} : </label>
                          <input type="text" disabled value="" name="post_code" class="form-control">
                        </div>
                  </div>
                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>
          <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Standards and formats') }}</h6>
                <strong>{{ __('Standards and formats are used to calculate product prices, shipping weights, and order times.') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    {{-- <!--div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Time zone') }} : </label>
                        <div class="col-lg-12">
                            <select disabled class="form-control selectric" name="timezone" id="timezone" >
                               <option value='UTC'>UTC</option>
                            </select>
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Select Default language') }} : </label>
                        <div class="col-lg-12">
                            <select disabled class="form-control selectric" name="default_language" id="default_language">
                              @foreach($languages ?? [] as $row)
                              <option value="{{ $row->code }}">{{ $row->name }}</option>
                              @endforeach
                            </select>
                        </div>
                    </div--> --}}
                    

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Default weight unit') }} : </label>
                        <div class="col-lg-12">
                            @php 
                              $weights = ['OZ, LBS, TONS'];
                              $weight_type = $weight_type->value??'LBS';
                            @endphp
                           {{-- <select disabled  class="form-control selectric" name="weight_type" id="weight_type">
                            @foreach($weights ?? [] as $row)
                            <option value="{{ $row }}" {{( $weight_type == $row) ? 'selected' : ''}}>{{ $row }}</option>
                            @endforeach
                          </select> --}}

                          <input type="text" disabled name="weight_type" value="OZ, LBS, TONS" class="form-control">

                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Default Measurment  unit') }} : </label>
                        <div class="col-lg-12">
                            @php 
                              $measurments = ['IN, FT, YDS'];
                              $measurment_type = $measurment_type->value??'IN';
                            @endphp
                           {{-- <select disabled  class="form-control selectric" name="measurment_type" id="measurment_type">
                            @foreach($measurments ?? [] as $row)
                            <option value="{{ $row }}" {{( $measurment_type == $row) ? 'selected' : ''}}>{{ $row }}</option>
                            @endforeach
                          </select> --}}

                          <input type="text" disabled name="measurment_type" value="IN, FT, YDS" class="form-control">

                        </div>
                    </div>

                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>
           <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store currency') }}</h6>
                <strong>{{ __('This is the currency your products are sold in') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store currency') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled name="currency_name" value="{{ $currency_info->currency_name ?? '' }}" class="form-control" required="">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Currency icon') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled name="currency_icon" value="{{ $currency_info->currency_icon ?? '' }}" class="form-control" required="">
                        </div>
                    </div>
                    @php
                    $currency_position=$currency_info->currency_position ?? '';
                    @endphp
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Currency Position') }} : </label>
                        <div class="col-lg-12">
                           <select class="form-control selectric" disabled name="currency_position">
                               <option value="left" @if($currency_position == 'left') selected="" @endif>{{ __('Left') }}</option>
                               <option value="right" @if($currency_position == 'right') selected="" @endif>{{ __('right') }}</option>
                           </select>
                        </div>
                    </div>
                    
                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>


         <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store Sale Tax Setting') }}</h6>
                <strong>{{ __('This is tax setting will be applied to All in state order') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                  
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Sales Tax Percentage Amount') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" value="{{ $tax ?? 0.00 }}"
                            name="tax" class="form-control"  id="tax" data-inputmask="'mask': '9{0,2}.9{0,3}[%]'" data-mask>
                        </div>
                    </div>

                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>


         <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store Shipping Setting') }}</h6>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">

                        <div class="from-group row mb-2">
                            <label for="" class="col-lg-12">{{ __('Is Free Shipping') }} : </label>
                            <div class="col-lg-12">
                                <select name="free_shipping" class="form-control">
                                    <option value="1" @if ($free_shipping == 1) selected @endif>
                                        {{ __('Enable') }}</option>
                                    <option value="0" @if ($free_shipping != 1) selected @endif>
                                        {{ __('Disable') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="from-group row mb-2">
                            <label for=""
                                class="col-lg-12">{{ __('Min Cart Total for free shipping') }} : </label>
                            <div class="col-lg-12">
                                <div class="input-with-icon">
                                <i class="fas fa-dollar-sign"></i>
                                <input type="number" step="any" value="{{ $min_cart_total ?? 100 }}"
                                    name="min_cart_total" class="form-control" placeholder="0.00">
                                </div>  
                                <small>{{ __('Your Minimum Cart total in store currency.') }}</small>
                            </div>
                        </div>



                        <div class="from-group row mb-2">
                            <label for="" class="col-lg-12">{{ __('Regular Shipping Method:') }}
                            </label>
                            @php
                                $shipping_types = ['weight_based' => 'Weight Based', 'per_item' => 'Per Item', 'flat_rate' => 'Flat Rate'];
                                
                                $shipping_info = json_decode($shipping_method->value, true);
                                $method = $shipping_info['method_type'];
                                $shipping_label = $shipping_info['label'];
                                $shipping_price = $shipping_info['pricing'];
                                $shipping_base_price = $shipping_info['base_pricing'];
                                
                                $countp = 1;
                            @endphp

                            <div class="col-lg-12">
                                <select name="shipping_method" id="shipping_type" class="select2 form-control">
                                    <option value=""> Choose Shipping Type</option>
                                    @foreach ($shipping_types as $stype => $label)
                                        <option @if ($method == $stype) selected @endif
                                            value="{{ $stype }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="from-group row mb-2">
                            <label for="" class="col-lg-12">{{ __('Shipping Method Label:') }} </label>

                            <div class="col-lg-12">
                                <input type="text" value="{{ $shipping_label ?? '' }}" required
                                    name="shipping_method_label" class="form-control" >
                                <small>{{ __('Your Shipping Method Label.') }}</small>
                            </div>
                        </div>


                        @foreach ($shipping_types as $stype => $label)
                            @php
                                $p = 0;
                                $display = 'display:none;';
                                if ($method == $stype) {
                                    $p = $shipping_price;
                                    $display = 'display:block;';
                                }
                            @endphp

                            @if ($stype == 'weight_based')
                                <div class="from-group row mb-2 type_price weight_based"
                                    style="{{ $display }}">
                                    <label for="" class="col-lg-12">{{ __('Price per LB :') }} </label>
                                    <div class="col-lg-12">
                                        <div class="input-with-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        <input type="number" required="" value="{{ $p }}"
                                            step="any" name="type_price['perlb']" class="form-control"
                                            placeholder="0.00">
                                        </div>    
                                        <small>{{ __('Your Shipping per LB in store currency.') }}</small>
                                    </div>
                                </div>
                                <div class="from-group row mb-2 type_price weight_based"
                                    style="{{ $display }}">
                                    <label for="" class="col-lg-12">{{ __('Shipping Base Price:') }}
                                    </label>

                                    <div class="col-lg-12">
                                        <div class="input-with-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        <input type="number" step="any"
                                            value="{{ $shipping_base_price ?? 0 }}" required
                                            name="base_price['perlb']" class="form-control" placeholder="0.00">
                                        </div>   
                                        <small>{{ __('Your Shipping Base Price.') }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($stype == 'per_item')
                                <div class="from-group row mb-2 type_price per_item"
                                    style="{{ $display }}">
                                    <label for="" class="col-lg-12">{{ __('Price per item :') }}
                                    </label>
                                    <div class="col-lg-12">
                                        <div class="input-with-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                         <input type="number" required="" value="{{ $p }}"
                                            step="any" name="type_price['per_item']" class="form-control"
                                            placeholder="0.00">
                                        </div>
                                        <small>{{ __('Your Shipping per item Price in store currency.') }}</small>
                                    </div>
                                </div>

                                <div class="from-group row mb-2 type_price per_item"
                                    style="{{ $display }}">
                                    <label for="" class="col-lg-12">{{ __('Shipping Base Price:') }}
                                    </label>

                                    <div class="col-lg-12">
                                        <div class="input-with-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        <input type="number" step="any"
                                            value="{{ $shipping_base_price ?? 0 }}" required
                                            name="base_price['per_item']" class="form-control" placeholder="0.00">
                                        </div>
                                        <small>{{ __('Your Shipping Base Price in store currency.') }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($stype == 'flat_rate')
                                <div class="from-group row mb-2 type_price flat_rate"
                                    style="{{ $display }}">
                                    <input type="hidden" value="0" name="base_price['flat_rate']">
                                    <label for=""
                                        class="col-lg-10">{{ __('flat Rate Shipping Price for Cart Totals :') }} </label>

                                    <div class="col-lg-12" id="flat_rate">
                                        @php
                                            
                                            $countp = 0;
                                            if (!is_array($p)) {
                                                $p = [];
                                                $p[] = ['from' => 0, 'to' => 25, 'price' => 10];
                                            }
                                            
                                        @endphp



                                        @foreach ($p as $k => $v)
                                            <div class="row mt-2" id="f-{{$k}}" >
                                                <div class="col-lg-3">
                                                      
                                                         <small>{{ __('Cart Subtotal Range (low)') }}</small>
                                                      
                                                        <div class="input-with-icon">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        <input type="number" required=""
                                                        value="{{ $v['from'] }}" step="any"
                                                        name="type_price['flatrate_range'][{{ $countp }}][from]"
                                                        class="form-control" placeholder="0.00">
                                                        </div>
                                                </div>
                                                <div class="col-lg-1">
                                                    <label for="">{{ __('-') }} </label>
                                                </div>
                                                <div class="col-lg-3">

                                                   
                                                         <small>{{ __('Cart Subtotal Range (high)') }}</small>
                            
                                                        <div class="input-with-icon">
                                                            <i class="fas fa-dollar-sign"></i>
                                                    <input type="number" required=""
                                                        value="{{ $v['to'] }}" step="any"
                                                        name="type_price['flatrate_range'][{{ $countp }}][to]"
                                                        class="form-control" placeholder="0.00">
                                                        </div>
                                                </div>
                                                <div class="col-lg-3">

                                                  
                                                        <small>{{ __('Shipping Cost to Customer') }}</small>
                                                   
                                                    <div class="input-with-icon">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    <input type="number" required=""
                                                        value="{{ $v['price'] }}" step="any"
                                                        name="type_price['flatrate_range'][{{ $countp }}][price]"
                                                        class="form-control" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                   
                                                    <a href="javascript:void(0)" data-rowid="f-{{$k}}" class="flatrate-remove-row"><i
                                                        class="fas fa-minus pt-4"></i></a>      
                                                </div>
                                            </div>
                                            @php
                                                $countp++;
                                            @endphp
                                        @endforeach

                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 pt-4" style="text-align: center;"><a href="javascript:void(0)" class="flatraterow"><i
                                            class="fas fa-3x fa-plus" style="font-size:2em;"></i></a></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach


                        <div class="from-group row mt-2">
                       
                            <div class="col-lg-4">
                               <button type="submit" class="basicbtn btn btn-primary">{{ __('Save changes') }}</button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            {{-- /right side --}}
        </div>



          <div class="row" >
            {{-- left side --}}
            {{-- <div class="col-lg-4">
                <h6>{{ __('Order Settings') }}</h6>
                <strong>{{ __('Configure your order methods and other settings') }}</strong>
            </div> --}}
            {{-- /left side --}}
            {{-- right side --}}
            {{-- <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Select Order Method') }} : </label>
                        <div class="col-lg-12">
                           <select  class="form-control selectric" name="order_method">
                            @if(tenant('push_notification') == 'on')
                               <option value="fmc" @if($order_method == 'fmc') selected="" @endif>{{ __('Real Time Push Notification') }}</option>
                            @endif   
                               <option value="mail" @if($order_method == 'mail') selected="" @endif>{{ __('Mail Notification') }}</option>
                               <option value="whatsapp" @if($order_method == 'whatsapp') selected="" @endif>{{ __('Whatsapp Notification') }}</option>
                           </select>
                        </div>
                    </div>
                   
                    <div class="from-group row mb-2">
                        @php
                        $shipping_amount_type= $order_settings->shipping_amount_type  ?? '';
                        @endphp
                        <label for="" class="col-lg-12">{{ __('Shipping Amount Type') }} : </label>
                        <div class="col-lg-12">
                            <select  class="form-control selectric" id="shipping_amount_type" name="shipping_amount_type">
                               <option value="shipping" @if($shipping_amount_type == 'shipping') selected @endif>{{ __('Based On  Shipping Charge') }}</option>
                               <option value="distance" @if($shipping_amount_type == 'distance') selected @endif>{{ __('Based On Distance') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="from-group row mb-2 google_api">
                        <label for="" class="col-lg-12">{{ __('Enter Google Place API') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" value="{{ $order_settings->google_api ?? '' }}" name="google_api" class="form-control google_api">
                        </div>
                    </div>
                    
                    <div class="from-group row mb-2 google_api_range">
                        <label for="" class="col-lg-12">{{ __('Delivery Fee (Per Kilo Meter)') }} : </label>
                        <div class="col-lg-12">
                            <input type="number" step="any" value="{{ $order_settings->delivery_fee ?? '' }}" name="delivery_fee" class="form-control ">
                        </div>
                    </div>

                    <div class="from-group row mb-2 google_api_range">
                        <label for="" class="col-lg-12">{{ __('Max Delivery Range (Meter)') }} : </label>
                        <div class="col-lg-12">
                            <input type="number" step="any" value="{{ $order_settings->google_api_range ?? '' }}" name="google_api_range" class="form-control ">
                        </div>
                    </div>
                    @php
                    $pickup_order= $order_settings->pickup_order ?? 'off';
                    $pre_order= $order_settings->pre_order ?? 'off';
                    $source_code= $order_settings->source_code ?? 'on';
                    
                    @endphp
                     <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Enable Pre Order') }} : </label>
                        <div class="col-lg-12">

                            <select class="form-control" name="pickup_order">
                                <option value="on" @if($pickup_order == 'on') selected="" @endif>{{  __('Enable') }}</option>
                                <option value="off" @if($pickup_order == 'off') selected="" @endif>{{  __('Disable') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Pickup Order Status') }} : </label>
                        <div class="col-lg-12">

                            <select class="form-control" name="pre_order">
                                <option value="on" @if($pre_order == 'on') selected="" @endif>{{  __('Enable') }}</option>
                                <option value="off" @if($pre_order == 'off') selected="" @endif>{{  __('Disable') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Disable Source Code For Checkout Page') }} : </label>
                        <div class="col-lg-12">

                            <select class="form-control" name="source_code">
                                <option value="on" @if($source_code == 'on') selected="" @endif>{{  __('Enable') }}</option>
                                <option value="off" @if($source_code == 'off') selected="" @endif>{{  __('Disable') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Whatsapp number for receiving order') }} : </label>
                        <div class="col-lg-12">
                            <input type="number" name="whatsapp_no" class="form-control" value="{{ $whatsapp_no->value ?? '' }}">
                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Average delivery time') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" name="delivery_time" value="{{ $average_times->delivery_time ?? '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Pickup time') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" name="pickup_time" value="{{ $average_times->pickup_time ?? '' }}" class="form-control">
                        </div>
                    </div>
                   

                    <div class="from-group row mt-2">
                       
                        <div class="col-lg-4">
                           <button type="submit" class="basicbtn btn btn-primary">{{ __('Save changes') }}</button>
                        </div>
                    </div>

                  </div>
               </div>
            </div> --}}
            {{-- /right side --}}
         </div>
           <div class="row" >
            {{-- left side --}}
            {{-- <div class="col-lg-4">
                <h6>{{ __('Whatsapp Settings') }}</h6>
                <strong>{{ __('Whatsapp Modules For Site ') }}</strong>
            </div> --}}
            {{-- /left side --}}
            {{-- right side --}}
            {{-- <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                   
                    
                   
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Whatsapp Number for contact') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" name="whatsapp_no" value="{{ $whatsapp_settings->whatsapp_no ?? '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Pretext For Product Page') }} : </label>
                        <div class="col-lg-12">
                          <textarea class="form-control h-150" required="" name="shop_page_pretext" placeholder="I want to purchase this">{{ $whatsapp_settings->shop_page_pretext ?? '' }}</textarea>

                          <span><span class="text-primary">{{ __('The Api Text Will Append Like This')  }} : <br> </span>{{ __('i want to purchase this product') }} http:://url.com/product/product-name</span>
                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Pretext For Other Page') }} : </label>
                        <div class="col-lg-12">
                          <textarea class="form-control h-150" required="" name="other_page_pretext" placeholder="I i have a query">{{ $whatsapp_settings->other_page_pretext ?? '' }}</textarea>
                        </div>
                    </div>
                    @php
                    $whatsapp_status=$whatsapp_settings->whatsapp_status ?? ''
                    @endphp
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Status') }} : </label>
                        <div class="col-lg-12">
                          <select class="form-control selectric" name="whatsapp_status">
                            <option value="on" @if($whatsapp_status == 'on') selected @endif >{{ __('Enable') }}</option>
                            <option value="off" @if($whatsapp_status == 'off') selected @endif>{{ __('Disable') }}</option>
                          </select>
                        </div>
                    </div>

                    <div class="from-group row mt-2">
                       
                        <div class="col-lg-4">
                           <button type="submit" class="basicbtn btn btn-primary">{{ __('Save changes') }}</button>
                        </div>
                    </div>

                  </div>
               </div>
            </div> --}}
            {{-- /right side --}}
         </div>
      </form>
   </div>
</div>
</section>

@endsection
@push('script')
<script>

$(document).ready(function () {
    Inputmask("9{0,2}.9{0,3}", {
                placeholder: "5.000",
                greedy: true
            }).mask('#tax');
        });
</script>
<script>
  "use strict";
    $('#timezone').val('{{ $timezone->value ?? '' }}')
    $('#default_language').val('{{ $default_language->value ?? '' }}');
   

    $(document).on('change','.input-with-icon input[type=number]',function() {
       // This function will be executed when the input value changes.
       var inputValue = $(this).val();
       
       inputValue = inputValue.match(/[0-9.]+/g);

       if(inputValue === null || inputValue === ''){
        return;
       }
       $(this).val(parseFloat(inputValue).toFixed(2));
    });


    $(document).on('change','#tax',function() {
       // This function will be executed when the input value changes.
       var inputValue = $(this).val();
       inputValue = inputValue.match(/[0-9.]+/g);

       if(inputValue === null || inputValue === ''){
        return;
       }
       $(this).val(parseFloat(inputValue).toFixed(3));
    });




    $('#shipping_amount_type').on('change',function(){
        var type=$(this).val();
        if (type == 'distance') {
            $('.google_api').show();
            $('.google_api_range').show();
        }
        else{
            $('.google_api').hide();
            $('.google_api_range').hide();
        }
    });

     var type=$('#shipping_amount_type').val();
   

        if (type == 'distance') {
            $('.google_api').show();
            $('.google_api_range').show();
        }
        else{
            $('.google_api').hide();
            $('.google_api_range').hide();
        }
    


        var rowtotal = {{ $countp }};
        //$(document).ready(function() {
            $('#shipping_type').change(function() {
                $('.type_price').hide();
                $('.' + $(this).val()).show();
            });
            $(document).on('click','.flatrate-remove-row', function() {
                $('#'+$(this).data('rowid')).remove();
                return false;
            });
            $('.flatraterow').on('click', function() {

                $('#flat_rate').append('<div class="row mt-2" id="f-'+rowtotal+'">' +
                    '<div class="col-lg-3"> <small> Cart Subtotal Range (low)</small><div class="input-with-icon"><i class="fas fa-dollar-sign"></i><input type="number"  value="" step="any" name="type_price[\'flatrate_range\'][' +
                    rowtotal + '][from]" class="form-control" placeholder="0.00"></div></div>' +
                    '<div class="col-lg-1"><label for="">-</label></div>' +
                    '<div class="col-lg-3"><small> Cart Subtotal Range (high)</small><div class="input-with-icon"><i class="fas fa-dollar-sign"></i><input type="number"  value="" step="any" name="type_price[\'flatrate_range\'][' +
                    rowtotal + '][to]" class="form-control" placeholder="0.00"></div></div>' +
                    '<div class="col-lg-3"><small>Shipping Cost to Customer</small> <div class="input-with-icon"><i class="fas fa-dollar-sign"></i><input type="number"  value="" step="any" name="type_price[\'flatrate_range\'][' +
                    rowtotal + '][price]" class="form-control" placeholder="0.00"></div></div>' +
                    ' <div class="col-lg-2"><a href="javascript:void(0)" data-rowid="f-'+rowtotal+'" class="flatrate-remove-row"><i class="fas fa-minus pt-4"></i></a></div> ' +
                    '</div>');

                rowtotal++;
            });

        //});

</script>
@endpush


