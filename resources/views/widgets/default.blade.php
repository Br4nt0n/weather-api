@inject('weather', 'App\Services\WeatherService')
<style>
    /* Widget Style */
    .weather-widget {
        text-align: center;
    }

    .weather-widget table {
        width: 100%;
        margin: 20px 0 0;
        text-align: left;
    }

    .weather-widget table tr td {
        padding: 5px 0;
        border-top: 1px solid #ddd;
    }

    .weather-widget table tr:last-child td {
        padding-bottom: 0;
    }

    .weather-widget .temp span {
        margin: 5px 0 5px 5px;
    }

    .weather-widget .degrees {
        display: block;
        font-size: 60px;
        line-height: 60px;
        width: auto;
    }

    .weather-widget .details {
        text-align: left;
        line-height: 20px;
        font-size: 12px;
        font-weight: normal;
        height: 60px;
    }

    .weather-widget .details em {
        font-style: normal;
    }

    .weather-widget h4 {
        margin: 0;
    }

    .weather-widget h5 {
        text-transform: uppercase;
    }
</style>

<div class="weather-widget">
    <h4>{{ $current['name'] }}</h4>

    <h5>{{ $current['weather'][0]['description'] }}</h5>
    @isset($current['main'])
    <div class="temp">
        Температура <span class="degrees">{{ ceil($current['main']['temp']) }}&deg;</span>
        По ощущениям <span class="degrees">{{ ceil($current['main']['feels_like']) }}&deg;</span><br>
        <span class="details">
            Давление <em class="pull-right">{{ $current['main']['pressure']}} мм.рт.ст.</em><br>
            Влажность <em class="pull-right">{{ ceil( $current['main']['humidity']) }}%</em><br>
            Облачность <em class="pull-right">{{ ceil($current['clouds']['all']) }}%</em><br>
            Ветер {{$weather->getWindDirection($current['wind']['deg'])}}
            <em class="pull-right">{{ ceil($current['wind']['speed'] ? $current['wind']['speed'] : '') }} м/с</em><br>
        </span>
    </div>
    @endisset
    @if($forecast['list'] > 1)
        <h5>Прогноз на ближайшие часы</h5>
        <table width="100%">
            <tr>
                <th>Время</th>
                <th>Температура</th>
                <th>Влажность</th>
                <th>Давление</th>
                <th>Погода</th>
                <th>Ветер</th>
            </tr>
            @foreach($forecast['list'] as $key => $value)
                <tr>
                    <td>{{ date('d.m.Y H:i', $value['dt']) }}</td>
                    <td class="text-right">{{ ceil($value['main']['temp']) }}&deg;</td>
                    <td class="text-right" style="opacity: 0.65;">{{ ceil( $value['main']['humidity']) }}%</td>
                    <td class="text-right">{{ $value['main']['pressure']}} мм.рт.ст.</td>
                    <td class="text-right">{{ $value['weather'][0]['description'] }}</td>
                    <td class="text-right">{{$weather->getWindDirection($value['wind']['deg'])}} {{ ceil($value['wind']['speed'] ? $value['wind']['speed'] : '') }}
                        м/с
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
</div>

