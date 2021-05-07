<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,500&display=swap" rel="stylesheet">

    <!-- Styles -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <?php
    use Illuminate\Support\Facades\Auth;


use Laravel\Cashier\Cashier;
use \Stripe\Stripe;
use App\Models\Firm;
  

// echo \DB::raw('POINT(140.7484404 -73.9878441)');

// if (isset($_COOKIE["vuex"])) {
//   $data = $_COOKIE["vuex"];
//   $serialized_data = serialize($data);
//   $size = strlen($serialized_data);
//   echo 'Length : ' . strlen($data);
//   echo "<br/>";
//   echo 'Size : ' . ($size * 8 / 1024) . ' Kb';
// }
      // $firm->subscriptions()->first()->cancel();

      // DB::table('users')
      //       ->where('company_id', $firm->id)
      //       ->update(['has_payed' => 0]);
        // $firm->subscriptions()->latest('created_at')->first()->cancel();
        // \Stripe\Stripe::setApiKey('sk_test_51IXWVQCYIxOqepVEeYb62gCSbIxSGhjuAY90LyCSS8QBF3CHh38tG5G2tHOsd7KRxzJjK9g45RoQ48DqiWQPa2JT00zk9hvtIY');
        //  $subscription = \Stripe\Subscription::retrieve('sub_JADgidUKSdyKUZ');
        //   $subscription->cancel();
        //   dd($subscription)


        // $sub_id = $firm->subscriptions()->first();
        // echo '<pre>';
        // echo print_r($subscriptions);
        // echo '</pre>';
     // $firm->subscriptions()->first()->resume();
     // echo $firm->invoices();
    ?>
    <div id="app">
            @yield('content')
    </div>
</body>
</html>
