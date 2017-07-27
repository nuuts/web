{!! session()->has('message') ? '<p>'.session()->get('message').'</p>' : '' !!}

        <!DOCTYPE html>
<html>
<head>
    <title>NAU</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            color: #38bdff;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 72px;
            margin-bottom: 40px;
        }

        .header {
            position: fixed;
            top: 0;
            height: 40px;
            border-bottom: 1px solid #38bdff;
            width: 96%;
            left: 0;
            padding: 2%;
        }

        .header-right {
            float: right;
            margin-right: 20px;
            color: black;
            font-size: 18px;
            font-weight: bold;
        }

        .offer {
            color: black;
            font-size: 25px;
            text-align: left;
            font-weight: bold;
            margin-bottom: 100px;
        }
    </style>
</head>
<body>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container">
    <div class="content">
        <div class="header">
            <div class="header-right">
                <a href="{{route('logout')}}">Logout</a>
            </div>
        </div>
        <div class="offer">
            @foreach ($data as $offer)
                <p>{{$offer->label}}</p>
                <p>{{$offer->description}}</p>
                <p> {{$offer->reward}}</p>
                <p>{{$offer->status}}</p>
                <p>{{$offer->start_date}} / {{$offer->start_time}}</p>
                <p>{{$offer->finish_date}} / {{$offer->finish_time}}</p>
                <p>{{$offer->category_id}}</p>
                //-------------------------------------------
            @endforeach
        </div>
        <div class="title">NAU</div>
    </div>
</div>
</body>
</html>
