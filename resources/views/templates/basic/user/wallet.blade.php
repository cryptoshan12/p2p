@extends($activeTemplate.'layouts.frontend')
@section('content')

    @include($activeTemplate.'partials.breadcrumb')
<script src="https://cdnjs.cloudflare.com/ajax/libs/web3/1.7.4-rc.2/web3.min.js" ></script>

    <section class="pt-120 pb-120 section--bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                  <div class="custom--card">
                    <div class="card-header border-bottom-0 text-center">
                        <ul class="btn-list justify-content-center">
                            <li><a href="{{route('user.wallets')}}" class="btn btn-sm btn-outline--base @if(!request()->id) active @endif">@lang('All')</a></li>
                            @foreach ($wallets as $wallet)
                                <li>
                                    <a href="{{route('user.wallets.single',[$wallet->crypto->id,$wallet->crypto->code])}}" class="btn btn-sm btn-outline--base @if(request()->id == $wallet->crypto->id) active @endif"><span>{{$wallet->crypto->code}}</span>@if ($cryptoWallets) ( {{ $cryptoWallets->where('crypto_id',$wallet->crypto_id)->count()}} ) @endif {{showAmount($wallet->balance,8)}}</a>
                                </li>
                            @endforeach
                        </ul>

                        @foreach ($wallets as $wallet)
                            @if(Request::routeIs('user.wallets.single') )
                                @if ($crypto->id == $wallet->crypto->id && $crypto->ismetamask == 0)
                                    <div class="text-center mt-4">
                                        <h4>@lang('Deposit Charge is') @if($wallet->crypto->dc_fixed > 0) {{$wallet->crypto->dc_fixed}} {{$wallet->crypto->code}} +  @endif {{$wallet->crypto->dc_percent}}%</h4>
                                    </div>

                                    <div class="mt-2 d-flex flex-wrap justify-content-center">

                                        <a href="{{route('user.wallets.generate',$wallet->crypto->code)}}" class="link-btn m-2"><i class="las la-plus"></i> @lang('Generate New') {{$wallet->crypto->code}} @lang('Address')</a>

                                        <a href="{{route('user.withdraw',$wallet->crypto->code)}}" class="link-btn m-2"><i class="las la-credit-card"></i> @lang('Withdraw') {{$wallet->crypto->code}}</a>

                                    </div>
                                @elseif ($crypto->id == $wallet->crypto->id && $crypto->ismetamask == 1)
                                    <div class="text-center mt-4">
                                        <h4>@lang('Deposit Charge is') @if($wallet->crypto->dc_fixed > 0) {{$wallet->crypto->dc_fixed}} {{$wallet->crypto->code}} +  @endif {{$wallet->crypto->dc_percent}}%</h4>
                                    </div>
                                    <div class="mt-2 d-flex flex-wrap justify-content-center">
                                        <input type='number' id='amount' value = '1' min ="1" placeholder="Token Amount"/>
                                    </div>
                                    <div class="mt-2 d-flex flex-wrap justify-content-center">

                                        <a href="#" id="payButton" class="link-btn m-2"><i class="las la-plus"></i> {{$wallet->crypto->code}} @lang('Deposit With MetaMask') </a>

                                        <a href="{{route('user.withdraw',$wallet->crypto->code)}}" class="link-btn m-2"><i class="las la-credit-card"></i> @lang('Withdraw') {{$wallet->crypto->code}}</a>
                                
                                    </div>
                                    <div class="mt-2 d-block justify-content-center">
                                        <p id='errorget' class='text-danger'></p>
                                        <p id='successget' class='text-success'></p>
                                       
                                    </div>
                                    <script>
                                        const web3 = new Web3(window.ethereum);
                                        const errordata = document.getElementById('errorget');
                                        const successget = document.getElementById('successget');
                                       // const status = document.getElementById('status');
                                        const payButton = document.getElementById('payButton');
                                        var dc_fix =  parseFloat({{$wallet->crypto->dc_fixed}});
                                        var dc_per = parseFloat({{$wallet->crypto->dc_percent}});
                                        let contract; var fromAddress;let chainId;let contractAddres;var decimals;var receiver ;
                                        let minABI = [
                                            // transfer
                                                {
                                                    "constant": false,
                                                    "inputs": [
                                                        {
                                                            "name": "_to",
                                                            "type": "address"
                                                        },
                                                        {
                                                            "name": "_value",
                                                            "type": "uint256"
                                                        }
                                                    ],
                                                    "name": "transfer",
                                                    "outputs": [
                                                        {
                                                            "name": "success",
                                                            "type": "bool"
                                                        }
                                                    ],
                                                    "payable": false,
                                                    "stateMutability": "nonpayable",
                                                    "type": "function"
                                                },
                                            // balanceOf
                                                {
                                                    "constant":true,
                                                    "inputs":[{"name":"_owner","type":"address"}],
                                                    "name":"balanceOf",
                                                    "outputs":[{"name":"balance","type":"uint256"}],
                                                    "type":"function"
                                                },
                                                // decimals
                                                {
                                                    "constant":true,
                                                    "inputs":[],
                                                    "name":"decimals",
                                                    "outputs":[{"name":"","type":"uint8"}],
                                                    "type":"function"
                                                }
                                            ];
                                        
                                        
                                         // add event listners
                                        window.addEventListener('load', function() {
                                            loginWithMetaMask()
                                        });
                                        window.addEventListener('DOMContentLoaded', () => {
                                          toggleButton();
                                        });
                                        function toggleButton() {
                                            if (!window.ethereum) {
                                                errordata.innerText = 'MetaMask is not installed'
                                                errordata.classList.remove('bg-purple-500', 'text-white')
                                                errordata.classList.add('bg-gray-500', 'text-gray-100', 'cursor-not-allowed')
                                                return false;
                                            }
                                            
                                        }
                                        async function loginWithMetaMask() {
        
                                            errordata.innerText = null
                                            successget.innerText = null
                                             chainId = await web3.eth.net.getId();
                                            if(chainId != '{{$metamaskConf->chain_id}}'){
                                                errordata.innerText = "We only support {{$metamaskConf->name}}";
                                                console.log("Chain Id = "+chainId);
                                                return false;
                                            }
                                            accounts = await window.ethereum.request({ method: 'eth_requestAccounts' })
                                            .catch((e) => {
                                                console.error(e.message)
                                                return
                                            })
                                            if (!accounts){ 
                                                return;
                                            }
                                            window.userWalletAddress = accounts[0];
                                            
                                            
                                             contractAddres="{{$crypto->bsc_address}}";//Bsc token
                                         fromAddress = accounts[0];
                                         receiver    = "{{$metamaskConf->wallet_address}}";   
                                         contract = new web3.eth.Contract(minABI, contractAddres, {from: fromAddress});
                                             try {
                                                 decimals = await contract.methods.decimals().call();
                                            } catch (e) {
                                                alert(e);
                                            }
                                        }
                                        //Pay with USDT
                                        payButton.addEventListener('click', () => {
                                            
                                            loginWithMetaMask();
                                            
                                            var amount_in = parseFloat(document.getElementById('amount').value);
                                            
                                            errordata.innerText = null;
                                            successget.innerText = null;
                            
                                            
                                            
                                            

                                           // let decimals = web3.utils.toBN(18); //for bsc
                                            //let amount = web3.utils.toBN(amount_in); //for bsc
                                           var BN = web3.utils.BN;
                                            var dc_per_charge = parseFloat(amount_in/100*dc_per);

                                            var youramount = parseFloat(amount_in+dc_fix)+dc_per_charge; //There you have to type your amount

                                            //var checked_amount = (youramount/10)*100; 
                                            
                                           let value= web3.utils.fromWei(toBaseUnit(youramount.toString(), decimals, web3.utils.BN), 'wei');

                                            // Call balanceOf function
                                            contract.methods.balanceOf(fromAddress).call((error, balance) => {
                                                contract.methods.decimals().call((error, decimals) => {
                                                  balance = balance/(10 ** decimals);
                                                  console.log(balance.toString());
                                                });
                                            });
                                            var gaslimit = {{$metamaskConf->gas_limit}};
                                            var networkconfirmations = {{$metamaskConf->network_confirmations}};
                                            // call transfer function
                                            contract.methods.transfer(receiver, value).send({
                                                from: fromAddress,
                                                to: receiver,
                                                gasLimit:gaslimit,
                                                
                                                
                                            })
                                            .on('transactionHash', function(hash){
                                              console.log(hash);
                                              g_hash = hash;
                                            })
                                            .then((txHash) => console.log(txHash))
                                            .then((txHash) => confirmEtherTransaction(g_hash,amount_in,networkconfirmations))
                                            .catch((e) => {
                                              //console.error(e.message)
                                              if(e.code == 4001){
                                               errordata.innerText = "Transaction Rejected";
                                               successget.innerText = "";
                                               return;
                                              }
                                              if(e.code == -32602){
                                               errordata.innerText = "Wallet Not Connected";
                                               successget.innerText = "";
                                               return;
                                              }
                                            })
                                            
                                            //Confirming The Transaction
                                            successget.innerText = "Waiting for confirmation";
                                        });
                                        function isString(s) {
                                          return (typeof s === 'string' || s instanceof String)
                                        }
                                        function toBaseUnit(value, decimals, BN) {
                                          if (!isString(value)) {
                                            throw new Error('Pass strings to prevent floating point precision issues.')
                                          }
                                          const ten = new BN(10);
                                          const base = ten.pow(new BN(decimals));
                                        
                                          // Is it negative?
                                          let negative = (value.substring(0, 1) === '-');
                                          if (negative) {
                                            value = value.substring(1);
                                          }
                                        
                                          if (value === '.') { 
                                            throw new Error(
                                            `Invalid value ${value} cannot be converted to`
                                            + ` base unit with ${decimals} decimals.`); 
                                          }
                                        
                                          // Split it into a whole and fractional part
                                          let comps = value.split('.');
                                          if (comps.length > 2) { throw new Error('Too many decimal points'); }
                                        
                                          let whole = comps[0], fraction = comps[1];
                                        
                                          if (!whole) { whole = '0'; }
                                          if (!fraction) { fraction = '0'; }
                                          if (fraction.length > decimals) { 
                                            throw new Error('Too many decimal places'); 
                                          }
                                        
                                          while (fraction.length < decimals) {
                                            fraction += '0';
                                          }
                                        
                                          whole = new BN(whole);
                                          fraction = new BN(fraction);
                                          let wei = (whole.mul(base)).add(fraction);
                                        
                                          if (negative) {
                                            wei = wei.neg();
                                          }
                                        
                                          return new BN(wei.toString(10), 10);
                                        }
                                        function confirmEtherTransaction(txHash,amount_in, confirmations) {
                                            setTimeout(async () => {
                                        
                                                // Get current number of confirmations and compare it with sought-for value
                                                const trxConfirmations = await getConfirmations(txHash)
                                                
                                                console.log('Transaction with hash ' + txHash + ' has ' + trxConfirmations + ' confirmation(s)')
                                                successget.innerText = "Confiming Transaction count is "+trxConfirmations;
                                            
                                                if (trxConfirmations >= confirmations) {
                                                    // Handle confirmation event according to your business logic
                                                  
                                                    console.log('Transaction with hash ' + txHash + ' has been successfully confirmed')
                                                    successget.innerText = "Transaction Succesfully confirmed";
                                                    
                                                    web3.eth.getTransaction(txHash, function(err, result) {
                                                        if (result.value) {
                                                            // $.get('/deposit/metamask', {
                                                            // amount: amount_in,
                                                            // crypto: {{$crypto->id}},
                                                            // wallet_id: {{$wallet->id}},
                                                            // details: "Deposit With Metamask",
                                                            // _token: '{{ csrf_token() }}',
                                                            // _method: 'PUT'
                                                            // },
                                                            // function(data,status){
                                                            //   alert("Status: " + data);
                                                            // });
                                                            
                                                            $.ajax({
                                                                url: '/user/deposit/metamask',
                                                                type: 'POST',
                                                                data: {
                                                                    _token: '{{ csrf_token() }}',
                                                                    amount: amount_in,
                                                                    crypto: {{$crypto->id}},
                                                                    wallet_id: {{$wallet->id}},
                                                                    details: "Deposit With Metamask",
                                                                },
                                                                dataType: 'JSON',
                                                                success: function (data) {
                                                                    console.log(data);
                                                                }
                                                            });
                                                            console.log(result);
                                                            
                                                            console.log(result.from);
                                                            console.log(result.to);
                                                            console.log(result.contractAddress);
                                                        }
                                                    });
                                            
                                                    return
                                                }
                                                // Recursive call
                                                return confirmEtherTransaction(txHash, confirmations)
                                            }, 10*1000)
                                        }
                                        async function getConfirmations(txHash) {
                                            try {
                                                // Get transaction details
                                                const trx = await web3.eth.getTransactionReceipt(txHash)//getTransaction
                                                
                                                // Get current block number
                                                const currentBlock = await web3.eth.getBlockNumber()
                                                
                                                // When transaction is unconfirmed, its block number is null.
                                                // In this case we return 0 as number of confirmations
                                                return trx.blockNumber === null ? 0 : currentBlock - trx.blockNumber
                                            }
                                            catch (error) {
                                                console.log(error)
                                            }
                                        }
                                    </script>
                                @endif
                            @endif
                        @endforeach
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive table-responsive--md">
                          <table class="table custom--table mb-0">
                            <thead>
                              <tr>
                                <th>@lang('Currency')</th>
                                <th>@lang('Generated at')</th>
                                <th>@lang('Wallet Address')</th>
                                <th>@lang('Action')</th>
                              </tr>
                            </thead>
                            <tbody>
                                 @if ($cryptoWallets)
                                  @forelse ($cryptoWallets as $cryptoWallet)
                                      <tr>
                                        <td data-label="@lang('Currency')">{{$cryptoWallet->crypto->code}}</td>
                                        <td data-label="@lang('Generated at')">{{showDateTime($cryptoWallet->created_at)}}</b></td>
                                        <td data-label="@lang('Wallet Address')">{{$cryptoWallet->wallet_address}}</td>
                                        <td data-label="@lang('Action')">
                                            <a href="javascript:void(0)" class="cmn-btn btn-sm copy-address" data-clipboard-text="{{$cryptoWallet->wallet_address}}"><i class="las la-copy"></i>@lang('Copy Address')</a>
                                        </td>
                                      </tr>
                                  @empty
                                      <tr>
                                          <td colspan="100%" class="text-center">{{__($emptyMessage)}}</td>
                                      </tr>
                                  @endforelse
                                  @endif
                            </tbody>
                          </table>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="row">
                @if ($cryptoWallets)
                {{ $cryptoWallets->links() }}
                @endif
            </div>
        </div>
    </section>
@endsection

@push('script-lib')
    <script src="{{asset($activeTemplateTrue.'js/clipboard.min.js')}}"></script>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";

            $('.copy-address').on('click',function () {
                var clipboard = new ClipboardJS('.copy-address');
                notify('success','Copied : '+$(this).data('clipboard-text'))
            })
        })(jQuery);
    </script>
@endpush
