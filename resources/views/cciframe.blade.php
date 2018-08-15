@extends('layouts.app')

@section('content')

    @php
        header('Location: '.$cc->getResponse()->getPaymentFormUrl());
    @endphp

    <div class="container">
        <form method="post" class="formular" id="paymentFrameForm" action="https://test-heidelpay.hpcgw.net/ngw/paymentFrame/sendData"
              enctype="application/json;charset=UTF-8">
            <?php
                if ($cc->getResponse()->isSuccess()) {
                    echo '<iframe id="paymentIframe" src="' . $cc->getResponse()->getPaymentFormUrl() . '" style="height:250px;"></iframe><br />';
                } else {
                    echo '<pre>' . print_r($cc->getResponse()->getError(), 1) . '</pre>';
                }
            ?>
            <button type="submit">Submit data</button>
        </form>
    </div>

    <script>

        var paymentFrameIframe = document.getElementById('paymentIframe');

        targetOrigin = getDomainFromUrl(paymentFrameIframe.src);


        paymentFrameForm = document.getElementById('paymentFrameForm');

        if (paymentFrameForm.addEventListener)
        {
            paymentFrameForm.addEventListener('submit', sendMessage);
        }
        else if (paymentFrameForm.attachEvent) { // IE DOM
            paymentFrameForm.attachEvent('onsubmit', sendMessage);
        }


        function sendMessage(e) {

            if(e.preventDefault) { e.preventDefault(); }
            else { e.returnValue = false; }

            var data = {};

            for (var i = 0, len = paymentFrameForm.length; i < len; ++i) {
                var input = paymentFrameForm[i];
                if (input.name) { data[input.name] = input.value; }

            }

            paymentFrameIframe.contentWindow.postMessage(JSON.stringify(data), targetOrigin);
        }

        function getDomainFromUrl(url) {
            var arr = url.split("/");
            return arr[0] + "//" + arr[2];
        }


        if (window.addEventListener) { // W3C DOM
            window.addEventListener('message', receiveMessage);

        }
        else if (window.attachEvent) { // IE DOM
            window.attachEvent('onmessage', receiveMessage);
        }

        /**
         * Define receiveMessage function
         *
         * This function will recieve the response message form the payment server.
         */
        function receiveMessage(e) {

            if (e.origin !== targetOrigin) {
                return;
            }

            var antwort = JSON.parse(e.data);
            console.log(antwort);
        }
    </script>


@endsection