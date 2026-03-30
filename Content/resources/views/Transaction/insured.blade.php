@extends('master')
@section('content')

<style>
.divider {
    height: 0.3%;
    width: 100%;
    background: red;
}
/* Vessel dropdown styling */
#vessel-wrapper {
    position: relative;
}
#vessel-suggestions {
    position: absolute;
    z-index: 9999;
    background: #fff;
    border: 1px solid #ced4da;
    border-top: none;
    width: 100%;
    max-height: 220px;
    overflow-y: auto;
    display: none;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
#vessel-suggestions .suggestion-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
}
#vessel-suggestions .suggestion-item:hover,
#vessel-suggestions .suggestion-item.active {
    background-color: #003478;
    color: #fff;
}
#vessel-error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 4px;
    display: none;
}
#vessel_display.is-invalid {
    border-color: #dc3545;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
/* ── Packing toggle ── */
document.addEventListener('DOMContentLoaded', function() {
    const packingSelect = document.getElementById('packingSelect');
    const otherPackingInput = document.getElementById('otherPackingInput');
    const otherPackingText = document.getElementById('otherPackingText');

    if (packingSelect && otherPackingInput && otherPackingText) {
        packingSelect.addEventListener('change', function() {
            if (this.value === 'Other') {
                otherPackingInput.style.display = 'block';
                otherPackingText.setAttribute('required', '');
            } else {
                otherPackingInput.style.display = 'none';
                otherPackingText.removeAttribute('required');
                otherPackingText.value = '';
            }
        });
    }
});

/* ── Currency calculations ── */
document.addEventListener('DOMContentLoaded', function() {
    const foreignCurrencyInput      = document.getElementById('si_fc');
    const incidentalPercentageInput = document.getElementById('inc_per');
    const incidentalChargesInput    = document.getElementById('inc_chrg');
    const tolerancePercentageInput  = document.getElementById('tol_per');
    const toleranceChargesInput     = document.getElementById('tolrence');
    const totalForeignCurrencyInput = document.getElementById('si_tfc');
    const exchangeRateInput         = document.getElementById('ex_rate');
    const sumInsuredRsInput         = document.getElementById('si_rs');

    function calculateTotalAndSumInsured() {
        const fc  = parseFloat(foreignCurrencyInput.value) || 0;
        const ic  = parseFloat(incidentalChargesInput.value) || 0;
        const tc  = parseFloat(toleranceChargesInput.value) || 0;
        const er  = parseFloat(exchangeRateInput.value) || 0;
        const tfc = fc + ic + tc;
        totalForeignCurrencyInput.value = tfc.toFixed(0);
        sumInsuredRsInput.value = (tfc * er).toFixed(0);
    }

    if (foreignCurrencyInput) {
        foreignCurrencyInput.addEventListener('input', function() {
            const v = parseFloat(this.value);
            if (!isNaN(v)) {
                incidentalPercentageInput.value = 10;
                const ic = v * 0.10;
                incidentalChargesInput.value = ic.toFixed(0);
                tolerancePercentageInput.value = 0;
                toleranceChargesInput.value = '0';
                calculateTotalAndSumInsured();
            } else {
                incidentalPercentageInput.value = '';
                incidentalChargesInput.value = '';
                tolerancePercentageInput.value = '';
                toleranceChargesInput.value = '';
            }
        });

        incidentalPercentageInput.addEventListener('input', function() {
            const pct = parseFloat(this.value);
            const fc  = parseFloat(foreignCurrencyInput.value);
            if (!isNaN(pct) && !isNaN(fc)) {
                incidentalChargesInput.value = (fc * (pct / 100)).toFixed(0);
                tolerancePercentageInput.value = 0;
                toleranceChargesInput.value = '0';
                calculateTotalAndSumInsured();
            } else {
                incidentalChargesInput.value = '';
            }
        });

        tolerancePercentageInput.addEventListener('input', function() {
            const pct = parseFloat(this.value);
            const fc  = parseFloat(foreignCurrencyInput.value);
            if (!isNaN(pct) && !isNaN(fc)) {
                toleranceChargesInput.value = (fc * (pct / 100)).toFixed(0);
                calculateTotalAndSumInsured();
            } else {
                toleranceChargesInput.value = '';
            }
        });
    }
});

/* ── Set today's date ── */
document.addEventListener('DOMContentLoaded', function() {
    const appDateInput = document.getElementById('app_date');
    if (appDateInput) {
        const today = new Date();
        const y = today.getFullYear();
        const m = String(today.getMonth() + 1).padStart(2, '0');
        const d = String(today.getDate()).padStart(2, '0');
        appDateInput.value = `${y}-${m}-${d}`;
    }
});

/* ── Exchange rate fetch ── */
document.addEventListener('DOMContentLoaded', function() {
    const currencyTypeSelect = document.getElementById('cur_type');
    const exchangeRateInput  = document.getElementById('ex_rate');
    const apiUrl = 'https://api.exchangerate-api.com/v4/latest/';

    currencyTypeSelect.addEventListener('change', function() {
        const selected = this.value;
        if (selected) {
            fetch(apiUrl + selected)
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(data => {
                    exchangeRateInput.value = data.rates?.PKR ?? '';
                    if (!data.rates?.PKR) alert('PKR rate not found for selected currency.');
                })
                .catch(() => {
                    exchangeRateInput.value = '';
                    alert('Failed to fetch exchange rate. Please try again.');
                });
        } else {
            exchangeRateInput.value = '';
        }
    });
});

/* ── MOP → Per Carry Limit ── */
const openPolData = @json($openPolData);

document.addEventListener('DOMContentLoaded', function() {
    const mopSelect    = document.getElementById('mop');
    const perCarryInput = document.getElementById('per_carry');

    mopSelect.addEventListener('change', function() {
        const idx = this.options[this.selectedIndex].getAttribute('data-index');
        perCarryInput.value = (idx !== null && openPolData[idx])
            ? Number(openPolData[idx].GGD_SINGLESHIPLIMIT).toLocaleString('en-US')
            : '';
    });
});

/* ── Peril table ── */
function togglePerilPer(checkbox, perilPerId) {
    const input = document.getElementById(perilPerId);
    input.value = '';
    input.readOnly = !checkbox.checked;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="selected_perils[]"]').forEach(cb => {
        const input = document.getElementById('peril_per_' + cb.value);
        if (input) input.readOnly = !cb.checked;
    });
});

function calculatePremium(perilPerInput, perilChrgId, siRsId) {
    const pct      = parseFloat(perilPerInput.value);
    const siInput  = document.getElementById(siRsId);
    const chrgInput = document.getElementById(perilChrgId);
    if (!isNaN(pct) && siInput && chrgInput) {
        const si = parseFloat(siInput.value);
        chrgInput.value = !isNaN(si) ? ((pct / 100) * si).toFixed(0) : '';
    } else if (chrgInput) {
        chrgInput.value = '';
    }
}

function calculateGrossPremium() {
    let total = 0;
    document.querySelectorAll('input[name="selected_perils[]"]:checked').forEach(cb => {
        const v = parseFloat(document.getElementById('peril_chrg_' + cb.value)?.value);
        if (!isNaN(v)) total += v;
    });
    document.getElementById('gross_pre').value = total.toFixed(0);

    let admin = total * 0.05;
    if (admin > 4000) admin = 4000;
    document.getElementById('admin_sur').value = admin.toFixed(0);
    document.getElementById('sub_total').value = (total + admin).toFixed(0);

    calculateGSTAndStampDutyAndNet();
}

function calculateGSTAndStampDutyAndNet() {
    const region    = document.getElementById('region').value;
    const subTotal  = parseFloat(document.getElementById('sub_total').value) || 0;
    const siRs      = parseFloat(document.getElementById('si_rs').value) || 0;

    let gstRate = 0, stampDutyRate = 0;
    if (region === 'Sindh')  { gstRate = 0.15; stampDutyRate = 0.00005; }
    if (region === 'Punjab') { gstRate = 0.16; stampDutyRate = 0.00005; }

    const gst       = subTotal * gstRate;
    const fif       = subTotal * 0.01;
    const stamp     = siRs * stampDutyRate;
    const net       = subTotal + gst + fif + stamp;

    document.getElementById('gst_per').value   = (gstRate * 100).toFixed(0);
    document.getElementById('gst').value        = gst.toFixed(0);
    document.getElementById('fif').value        = fif.toFixed(0);
    document.getElementById('stamp_duty').value = stamp.toFixed(0);
    document.getElementById('net_pre').value    = net.toFixed(0);
}

/* ════════════════════════════════════════════════
   VESSEL SEARCH — show NAME, store CODE, validate
   ════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {
    // Build vessel data array from Laravel (code → name mapping)
    const vesselData = [
        @foreach($vessels as $row)
        { code: "{{ addslashes($row->PVC_CODE) }}", name: "{{ addslashes($row->PVC_DESC) }}" },
        @endforeach
    ];

    const displayInput  = document.getElementById('vessel_display');
    const codeInput     = document.getElementById('vessel');          // hidden — holds the code
    const suggestionsBox = document.getElementById('vessel-suggestions');
    const errorMsg      = document.getElementById('vessel-error');

    let activeIndex = -1;
    let isValidSelection = false;

    function showError(msg) {
        errorMsg.textContent = msg;
        errorMsg.style.display = 'block';
        displayInput.classList.add('is-invalid');
    }
    function clearError() {
        errorMsg.style.display = 'none';
        displayInput.classList.remove('is-invalid');
    }
    function closeSuggestions() {
        suggestionsBox.style.display = 'none';
        suggestionsBox.innerHTML = '';
        activeIndex = -1;
    }
    function renderSuggestions(list) {
        suggestionsBox.innerHTML = '';
        if (!list.length) { closeSuggestions(); return; }
        list.forEach((v, i) => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.textContent = v.name + ' (' + v.code + ')';
            div.addEventListener('mousedown', function(e) {
                e.preventDefault(); // prevent blur firing before click
                selectVessel(v);
            });
            suggestionsBox.appendChild(div);
        });
        suggestionsBox.style.display = 'block';
        activeIndex = -1;
    }

    function selectVessel(v) {
        displayInput.value   = v.name;   // show the name
        codeInput.value      = v.code;   // store the code
        isValidSelection     = true;
        closeSuggestions();
        clearError();
    }

    // Typing in the display input
    displayInput.addEventListener('input', function() {
        const q = this.value.trim().toUpperCase();
        isValidSelection = false;
        codeInput.value  = '';

        if (!q) { closeSuggestions(); return; }

        const matches = vesselData.filter(v =>
            v.name.toUpperCase().includes(q) || v.code.toUpperCase().includes(q)
        ).slice(0, 50); // cap at 50 results

        renderSuggestions(matches);
    });

    // Keyboard navigation
    displayInput.addEventListener('keydown', function(e) {
        const items = suggestionsBox.querySelectorAll('.suggestion-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, items.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0) items[activeIndex].dispatchEvent(new Event('mousedown'));
            return;
        } else if (e.key === 'Escape') {
            closeSuggestions();
            return;
        }
        items.forEach((item, i) => item.classList.toggle('active', i === activeIndex));
        if (activeIndex >= 0) items[activeIndex].scrollIntoView({ block: 'nearest' });
    });

    // Validate on blur
    displayInput.addEventListener('blur', function() {
        setTimeout(function() { // timeout lets mousedown fire first
            closeSuggestions();
            if (displayInput.value.trim() === '') {
                showError('Vessel / Carrier is required.');
                isValidSelection = false;
                codeInput.value  = '';
            } else if (!isValidSelection) {
                showError('Please select a vessel from the list.');
                displayInput.value = '';
                codeInput.value    = '';
            } else {
                clearError();
            }
        }, 150);
    });

    // Prevent form submission if vessel invalid
    document.getElementById('my-form').addEventListener('submit', function(e) {
        if (!isValidSelection || !codeInput.value) {
            e.preventDefault();
            if (displayInput.value.trim() === '') {
                showError('Vessel / Carrier is required.');
            } else {
                showError('Please select a valid vessel from the list.');
            }
            displayInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            displayInput.focus();
        }
    });

    // Close suggestions when clicking elsewhere
    document.addEventListener('click', function(e) {
        if (!document.getElementById('vessel-wrapper').contains(e.target)) {
            closeSuggestions();
        }
    });
});
</script>

<div class="content-body">
<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">

<div class="card">
<div class="card-header">
    <h4 class="card-title">Create Marine Certificate</h4>
</div>
<div class="card-body">
<div class="basic-form">
<form class="form-horizontal" role="form" method="POST" action="{{ url('/addInsured') }}" id="my-form" autocomplete="off" enctype="multipart/form-data">
    {!! csrf_field() !!}

    
    @if(isset($invoiceId) && $invoiceId)
        <input type="hidden" name="invoice_id" value="{{ $invoiceId }}">
    @endif

    <h3>Insured Information</h3>
    <br>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>MOP</label>
            <select class="form-control" name="mop" id="mop" required>
                <option disabled selected value="">Choose MOP</option>
                @foreach($openPolData as $index => $row)
                    <option value="{{ $row->GDH_DOC_REFERENCE_NO }}" data-index="{{ $index }}">
                        {{ $row->GDH_DOC_REFERENCE_NO }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Portal Number</label>
            <input type="text" class="form-control" name="portal_no" id="portal_no" value="{{ $next_cvrnum }}" style="background-color:#f0f0f0;" readonly>
        </div>
        <div class="form-group col-md-4">
            <label>Issue Date</label>
            <input type="date" class="form-control" name="app_date" id="app_date" style="background-color:#f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Business Line</label>
            <input type="text" class="form-control" name="bus_line" id="bus_line" value="Marine" style="background-color:#f0f0f0;" readonly>
        </div>
        <div class="form-group col-md-4">
            <label>Business Class</label>
            <input type="text" class="form-control" name="bus_class" id="bus_class" value="{{ $openPolData[0]->PBC_DESC }}" style="background-color:#f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Bank</label>
            <select class="form-control" name="bank[]" id="bank" multiple required>
                @foreach($mop_banks as $row)
                    <option value="{{ $row->PBN_BNK_CODE }}">{{ $row->PBN_BNK_DESC }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Insured Name</label>
            <input type="text" class="form-control" name="insured" id="insured" value="{{ $openPolData[0]->PPS_DESC }}" style="background-color:#f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Address</label>
            <textarea class="form-control" name="address" style="background-color:#f0f0f0;" readonly>{{ $openPolData[0]->PAS_ADDRESS1 }}</textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Category</label>
            <select class="form-control" name="mar_catg" required>
                <option disabled selected value="">Choose Category</option>
                <option value="Import">Import</option>
                <option value="Export">Export</option>
                <option value="Inland">Inland</option>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Per Carry Limit</label>
            <input type="text" class="form-control" name="per_carry" id="per_carry" style="background-color:#f0f0f0;" readonly>
        </div>
    </div>

    <br>
    <h3>Consignment Information</h3>
    <br>

    
    <div class="form-row">
        <div class="form-group col-md-4">
            <label>PO No.</label>
            <input type="text" class="form-control" placeholder="PO No" name="po_no" id="po_no">
        </div>
  
        <input type="hidden" name="voyage_no" value="">
        <input type="hidden" name="arr_date" value="">
        <input type="hidden" name="inv_date" value="">
        <input type="hidden" name="via" value="">
        <input type="hidden" name="builty_no" value="">
        <input type="hidden" name="builty_date" value="">
        <input type="hidden" name="add_terms" value="">
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
    <label>Invoice No.</label>
    <input type="text" class="form-control" placeholder="Invoice No"
           name="inv_no" id="inv_no" required
           value="{{ $invoiceData->invoice_number ?? '' }}"
           {{ isset($invoiceData) && $invoiceData ? 'style=background-color:#f0f0f0; readonly' : '' }}>
</div>
       
        <div class="form-group col-md-4">
            <label>Conveyance</label>
            <select class="form-control" name="conv" multiple required>
                <option disabled value="">Choose Conveyance</option>
                <option value="Air">By Air</option>
                <option value="Sea">By Sea</option>
                <option value="RoadRail">By Road/Rail</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>BL/AWB No.</label>
            <input type="text" class="form-control" placeholder="BL/AWB No" name="bl_no" id="bl_no" required>
        </div>
        <div class="form-group col-md-4">
            <label>BL/AWB Date</label>
            <input type="date" class="form-control" name="bl_date" id="bl_date" required>
        </div>
        <div class="form-group col-md-4">
            <label>Packing</label>
            <select class="form-control" name="packingSelect" id="packingSelect" required>
                <option disabled selected value="">Choose Packing</option>
                <option value="Standard">In Standard Packing</option>
                <option value="Container">In Container Only</option>
                <option value="Other">Other</option>
            </select>
            <div id="otherPackingInput" style="display:none;">
                <label for="otherPackingText">Specify Packing</label>
                <input type="text" class="form-control" name="otherPackingText" id="otherPackingText">
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Voyage From</label>
            <input type="text" class="form-control" placeholder="Voyage From" name="voyage_from" id="voyage_from" required>
        </div>
        <div class="form-group col-md-4">
            <label>Voyage To</label>
            <input type="text" class="form-control" placeholder="Voyage To" name="voyage_to" id="voyage_to" required>
        </div>
    </div>

   
    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Vessel / Carrier <span class="text-danger">*</span></label>
            <div id="vessel-wrapper">
               
                <input type="text"
                       id="vessel_display"
                       class="form-control"
                       placeholder="Type to search vessel..."
                       autocomplete="off">
          
                <input type="hidden" name="vessel" id="vessel">
                <div id="vessel-suggestions"></div>
                <div id="vessel-error"></div>
            </div>
        </div>
        <div class="form-group col-md-4">
            <label>LC Number</label>
            <input type="text" class="form-control" placeholder="LC Number" name="lc_no" id="lc_no">
        </div>
        <div class="form-group col-md-4">
            <label>LC Date</label>
            <input type="date" class="form-control" name="lc_date" id="lc_date">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Incoterms</label>
            <select class="form-control" name="incoterms" id="incoterms" required>
                <option disabled selected value="">Choose Incoterms</option>
                <option value="004">CIF</option>
                <option value="001">FOB</option>
                <option value="002">C&F</option>
                <option value="003">Freight</option>
                <option value="005">CIP</option>
                <option value="006">EXW</option>
                <option value="007">CPT</option>
                <option value="008">CFR</option>
                <option value="009">FCA</option>
                <option value="010">DAP</option>
                <option value="011">DDP</option>
                <option value="012">DDU</option>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Sailing/Departure/Dispatch on/or about</label>
            <input type="date" class="form-control" name="salling" id="salling" required>
        </div>
    </div>

   
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Subject Matter Insured</label>
            <textarea class="form-control" placeholder="Subject Matter Insured" name="sub_mat" required></textarea>
        </div>
    </div>

    <br>
    <h3>Basis Of Valuation</h3>
    <br>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Currency Type</label>
            <select class="form-control" name="cur_type" id="cur_type" required>
                <option disabled selected value="">Choose Currency Type</option>
                <option value="USD">United States Dollar</option>
                <option value="EUR">Euro</option>
                <option value="AED">UAE Dirham</option>
                <option value="GBP">Pound Sterling</option>
                <option value="AUD">Australian Dollar</option>
                <option value="CAD">Canadian Dollar</option>
                <option value="CNY">Chinese Renminbi</option>
                <option value="JPY">Japanese Yen</option>
                <option value="SAR">Saudi Riyal</option>
                <option value="THB">Thai Baht</option>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Exchange Rate</label>
            <input type="text" class="form-control" placeholder="Exchange Rate" name="ex_rate" id="ex_rate" required>
        </div>
        <div class="form-group col-md-4">
            <label>Foreign Currency</label>
            <input type="number" class="form-control" placeholder="Foreign Currency" name="si_fc" id="si_fc" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Incidental Charges</label>
            <div class="row">
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="%" name="inc_per" id="inc_per" required>
                </div>
                <div class="col-md-6">
                    <input type="number" class="form-control" placeholder="Incidental Charges" name="inc_chrg" id="inc_chrg" style="background-color:#f0f0f0;" readonly>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Tolerance</label>
            <div class="row">
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="%" name="tol_per" id="tol_per" required>
                </div>
                <div class="col-md-6">
                    <input type="number" class="form-control" placeholder="Tolerance Charges" name="tolrence" id="tolrence" style="background-color:#f0f0f0;" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Total Foreign Currency Value</label>
            <input type="number" class="form-control" name="si_tfc" id="si_tfc" style="background-color:#f0f0f0;" readonly>
        </div>
        <div class="form-group col-md-2"></div>
        <div class="form-group col-md-4">
            <label>Sum Insured (Rs)</label>
            <input type="number" class="form-control" name="si_rs" id="si_rs" style="background-color:#f0f0f0;" readonly>
        </div>
    </div>

    <table class="table table-bordered">
        <thead style="background:#003478 !important;">
            <tr>
                <th style="color:#FFFFFF !important;"><strong>Peril</strong></th>
                <th style="color:#FFFFFF !important;"><strong>%</strong></th>
                <th style="color:#FFFFFF !important;"><strong>Calculated Premium</strong></th>
                <th style="color:#FFFFFF !important;"><strong>Action</strong></th>
            </tr>
        </thead>
        <tbody>
        @php $count = 1; @endphp
        @foreach($perils as $row)
        <tr>
            <td>
                <input type="hidden" name="peril_code[]" value="{{ $row->GAC_ACTION_CODE }}">
                <input type="text"   name="peril_dsc[]"  value="{{ $row->PTF_SHORTDESC }}" style="border:none;" readonly>
            </td>
            <td>
                <input type="number" name="peril_per[]" id="peril_per_{{ $count - 1 }}" step="any"
                       oninput="calculatePremium(this, 'peril_chrg_{{ $count - 1 }}', 'si_rs'); calculateGrossPremium()"
                       value="{{ $row->GAC_RATE }}">
            </td>
            <td>
                <input type="number" name="peril_chrg[]" id="peril_chrg_{{ $count - 1 }}" style="border:none;" readonly>
            </td>
            <td>
                <input type="checkbox" name="selected_perils[]" value="{{ $count - 1 }}"
                       onchange="togglePerilPer(this, 'peril_per_{{ $count - 1 }}'); calculateGrossPremium()" checked>
            </td>
        </tr>
        @php $count++; @endphp
        @endforeach
        </tbody>
    </table>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Gross Premium</label>
            <input type="number" class="form-control" name="gross_pre" id="gross_pre" style="background-color:#f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Admin</label>
            <input type="number" class="form-control" name="admin_sur" id="admin_sur" style="background-color:#f0f0f0;" readonly>
        </div>
        <div class="form-group col-md-4">
            <label>Sub Total</label>
            <input type="number" class="form-control" name="sub_total" id="sub_total" style="background-color:#f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Region</label>
            <select class="form-control" name="region" id="region" onchange="calculateGSTAndStampDutyAndNet()" required>
                <option disabled selected value="">Choose Region</option>
                <option value="Sindh">Sindh</option>
                <option value="Punjab">Punjab</option>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>GST</label>
            <input type="hidden" class="form-control" name="gst_per" id="gst_per" readonly>
            <input type="number" class="form-control" name="gst" id="gst" style="background-color:#f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>F.I.F</label>
            <input type="number" class="form-control" name="fif" id="fif" style="background-color:#f0f0f0;" readonly>
        </div>
        <div class="form-group col-md-4">
            <label>Stamp Duty</label>
            <input type="number" class="form-control" name="stamp_duty" id="stamp_duty" style="background-color:#f0f0f0;" readonly>
        </div>
        <div class="form-group col-md-4">
            <label>Net Premium</label>
            <input type="number" class="form-control" name="net_pre" id="net_pre" style="background-color:#f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Document Description</label>
            <textarea class="form-control" placeholder="Document Description" name="doc_desc"></textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

@endsection