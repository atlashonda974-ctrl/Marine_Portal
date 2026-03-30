@extends('master')
@section('content')

<style>
    .divider {
        height: 0.3%;
        width: 100%;
        background: red;
    }
</style>

<script>
 document.addEventListener('DOMContentLoaded', function() {
        const packingSelect = document.getElementById('packingSelect');
        const otherPackingInput = document.getElementById('otherPackingInput');
        const otherPackingText = document.getElementById('otherPackingText');

        if (packingSelect && otherPackingInput && otherPackingText) {
            packingSelect.addEventListener('change', function() {
                console.log('Select value changed:', this.value);
                if (this.value === 'Other') {
                    otherPackingInput.style.display = 'block';
                    otherPackingText.setAttribute('required', '');
                } else {
                    otherPackingInput.style.display = 'none';
                    otherPackingText.removeAttribute('required');
                    otherPackingText.value = '';
                }
            });

            console.log('Event listener attached to packingSelect');
        } else {
            console.error('One or more elements not found!');
        }
});

document.addEventListener('DOMContentLoaded', function() {
    const foreignCurrencyInput = document.getElementById('si_fc');
    const incidentalPercentageInput = document.getElementById('inc_per');
    const incidentalChargesInput = document.getElementById('inc_chrg');
    const tolerancePercentageInput = document.getElementById('tol_per');
    const toleranceChargesInput = document.getElementById('tolrence');

    const totalForeignCurrencyInput = document.getElementById('si_tfc');
    const exchangeRateInput = document.getElementById('ex_rate');
    const sumInsuredRsInput = document.getElementById('si_rs');

    function calculateTotalAndSumInsured() {
        const foreignCurrencyValue = parseFloat(foreignCurrencyInput.value) || 0;
        const incidentalChargeValue = parseFloat(incidentalChargesInput.value) || 0;
        const toleranceChargeValue = parseFloat(toleranceChargesInput.value) || 0;
        const exchangeRateValue = parseFloat(exchangeRateInput.value) || 0;

        const totalForeignCurrency = foreignCurrencyValue + incidentalChargeValue + toleranceChargeValue;
        totalForeignCurrencyInput.value = totalForeignCurrency.toFixed(0);

        const sumInsuredRs = totalForeignCurrency * exchangeRateValue;
        sumInsuredRsInput.value = sumInsuredRs.toFixed(0);
    }

    if (foreignCurrencyInput && incidentalPercentageInput && incidentalChargesInput && tolerancePercentageInput && toleranceChargesInput) {
        foreignCurrencyInput.addEventListener('input', function() {
            const foreignCurrencyValue = parseFloat(this.value);
            if (!isNaN(foreignCurrencyValue)) {
                incidentalPercentageInput.value = 10;
                const incidentalCharge = foreignCurrencyValue * 0.10;
                incidentalChargesInput.value = incidentalCharge.toFixed(0);

                tolerancePercentageInput.value = 10;
                const fcic = incidentalCharge + foreignCurrencyValue;
                const toleranceCharge = fcic * 0.10;
                toleranceChargesInput.value = toleranceCharge.toFixed(0);

                calculateTotalAndSumInsured();
            } else {
                incidentalPercentageInput.value = '';
                incidentalChargesInput.value = '';
                tolerancePercentageInput.value = '';
                toleranceChargesInput.value = '';
            }
        });

        incidentalPercentageInput.addEventListener('input', function() {
            const incidentalPercentageValue = parseFloat(this.value);
            const foreignCurrencyValue = parseFloat(foreignCurrencyInput.value);

            if (!isNaN(incidentalPercentageValue) && !isNaN(foreignCurrencyValue)) {
                const incidentalCharge = foreignCurrencyValue * (incidentalPercentageValue / 100);
                incidentalChargesInput.value = incidentalCharge.toFixed(0);

                const fcic = incidentalCharge + foreignCurrencyValue;
                tolerancePercentageInput.value = 10;
                const toleranceCharge = fcic * 0.10;
                toleranceChargesInput.value = toleranceCharge.toFixed(0); 

                calculateTotalAndSumInsured();
            } else {
                incidentalChargesInput.value = '';
            }
        });

        tolerancePercentageInput.addEventListener('input', function() {
            const tolerancePercentageValue = parseFloat(this.value);
            const foreignCurrencyValue = parseFloat(foreignCurrencyInput.value);

            if (!isNaN(tolerancePercentageValue) && !isNaN(foreignCurrencyValue)) {
                const toleranceCharge = foreignCurrencyValue * (tolerancePercentageValue / 100);
                toleranceChargesInput.value = toleranceCharge.toFixed(0);

                calculateTotalAndSumInsured();
            } else {
                toleranceChargesInput.value = '';
            }
        });
    } else {
        console.error('One or more input elements not found!');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const appDateInput = document.getElementById('app_date');
    if (appDateInput) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const currentDate = `${year}-${month}-${day}`;
        appDateInput.value = currentDate;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const currencyTypeSelect = document.getElementById('cur_type');
    const exchangeRateInput = document.getElementById('ex_rate');
    const apiUrl = 'https://api.exchangerate-api.com/v4/latest/';

    if (currencyTypeSelect && exchangeRateInput) {
        currencyTypeSelect.addEventListener('change', function() {
            const selectedCurrency = this.value;

            if (selectedCurrency) {
                fetch(apiUrl + selectedCurrency)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.rates && data.rates.PKR !== undefined) {
                            exchangeRateInput.value = data.rates.PKR;
                        } else {
                            exchangeRateInput.value = '';
                            alert('PKR rate not found for the selected currency.');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching exchange rate:', error);
                        exchangeRateInput.value = '';
                        alert('Failed to fetch exchange rate. Please try again.');
                    });
            } else {
                exchangeRateInput.value = '';
            }
        });
    }
});

function updateVesselCode() {
    const descInput = document.getElementById('vessel');
    const codeInput = document.getElementById('vessel_code');
    const datalist = document.getElementById('vessels');
    const options = datalist.options;
    const selectedDesc = descInput.value;

    for (let i = 0; i < options.length; i++) {
        if (options[i].value === selectedDesc) {
            codeInput.value = options[i].getAttribute('data-code');
            return;
        }
    }
    codeInput.value = '';
}

function filterFunction(input, event) {
    const datalistId = input.getAttribute("list");
    const datalist = document.getElementById(datalistId);
    const options = datalist.options;
    const filter = input.value.toUpperCase();

    for (let i = 0; i < options.length; i++) {
        const optionValue = options[i].value.toUpperCase();
        if (optionValue.indexOf(filter) > -1) {
            options[i].style.display = "";
        } else {
            options[i].style.display = "none";
        }
    }

    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        return;
    }
}

function togglePerilPer(checkbox, perilPerId) {
    const perilPerInput = document.getElementById(perilPerId);
    perilPerInput.disabled = !checkbox.checked;
}

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_perils[]"]');
    checkboxes.forEach(checkbox => {
        const perilPerId = 'peril_per_' + checkbox.value;
        const perilPerInput = document.getElementById(perilPerId);
        perilPerInput.disabled = !checkbox.checked;
    });
});

function calculatePremium(perilPerInput, perilChrgId, siRsId) {
    const perilPercentage = parseFloat(perilPerInput.value);
    const sumInsuredInput = document.getElementById(siRsId);
    const calculatedPremiumInput = document.getElementById(perilChrgId);

    if (!isNaN(perilPercentage) && sumInsuredInput && calculatedPremiumInput) {
        const sumInsured = parseFloat(sumInsuredInput.value);
        if (!isNaN(sumInsured)) {
            const calculatedPremium = (perilPercentage / 100) * sumInsured;
            calculatedPremiumInput.value = calculatedPremium.toFixed(0);
        } else {
            calculatedPremiumInput.value = '';
        }
    } else {
        calculatedPremiumInput.value = '';
    }
}

function calculateGrossPremium() {
    const checkedCheckboxes = document.querySelectorAll('input[name="selected_perils[]"]:checked');
    let totalPremium = 0;

    checkedCheckboxes.forEach(checkbox => {
        const rowIndex = checkbox.value;
        const premiumInputId = 'peril_chrg_' + rowIndex;
        const premiumInput = document.getElementById(premiumInputId);

        if (premiumInput && !isNaN(parseFloat(premiumInput.value))) {
            totalPremium += parseFloat(premiumInput.value);
        }
    });

    document.getElementById('gross_pre').value = totalPremium.toFixed(0);
    
    let adminSurcharge = totalPremium * 0.05;
    if (adminSurcharge > 4000) {
        adminSurcharge = 4000;
    }
    document.getElementById('admin_sur').value = adminSurcharge.toFixed(0);

    const grossPremium = parseFloat(document.getElementById('gross_pre').value) || 0;
    const admin = parseFloat(document.getElementById('admin_sur').value) || 0;
    document.getElementById('sub_total').value = (grossPremium + admin).toFixed(0);

    calculateGSTAndStampDutyAndNet();
}

function calculateGSTAndStampDutyAndNet() {
    const regionSelect = document.getElementById('region');
    const subTotalInput = document.getElementById('sub_total');
    const gstInput = document.getElementById('gst');
    const stampDutyInput = document.getElementById('stamp_duty');
    const fifInput = document.getElementById('fif');
    const netPremiumInput = document.getElementById('net_pre');

    const subTotal = parseFloat(subTotalInput.value) || 0;
    let gstRate = 0;
    let stampDutyRate = 0;

    if (regionSelect.value === 'Sindh') {
        gstRate = 0.15;
        stampDutyRate = 0.00006;
    } else if (regionSelect.value === 'Punjab') {
        gstRate = 0.16;
        stampDutyRate = 0.00006;
    }

    const gstAmount = subTotal * gstRate;
    gstInput.value = gstAmount.toFixed(0);

    const stampDutyAmount = subTotal * stampDutyRate;
    stampDutyInput.value = stampDutyAmount.toFixed(0);

    const fifAmount = subTotal * 0.01;
    fifInput.value = fifAmount.toFixed(0);

    const netPremium = subTotal + gstAmount + fifAmount + stampDutyAmount;
    netPremiumInput.value = netPremium.toFixed(0);
}

function validateForm() {
    const requiredFields = ['mar_catg', 'cur_type', 'incoterms', 'bank', 'region'];
    let isValid = true;
    
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && !field.value) {
            field.style.borderColor = 'red';
            isValid = false;
        } else if (field) {
            field.style.borderColor = '';
        }
    });
    
    if (!isValid) {
        alert('Please fill in all required fields marked in red.');
        return false;
    }
    
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('my-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    }
});
</script>

<div class="content-body">
<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">

@if(empty($openPolData) || empty($pol))
    <div class="alert alert-warning">
        <strong>Note:</strong> Creating new certificate. Please fill in all required fields.
    </div>
@endif

<div class="card">
<div class="card-header">
<h4 class="card-title">Marine Certificate</h4>
</div>
<div class="card-body">
<div class="basic-form">
<form class="form-horizontal" role="form" method="POST" action="{{ url('/AddInsured') }}" id="my-form" autocomplete="off" enctype="multipart/form-data">
    {!! csrf_field() !!}

    <h3> Insured Information</h3>
    <br>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>MOP</label>
            <input type="text" class="form-control" placeholder="MOP" name="mop" id="mop" 
                   value="{{ isset($openPolData[0]) ? $openPolData[0]->GDH_DOC_REFERENCE_NO : '' }}" 
                   style="background-color: #f0f0f0;" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Portal Number</label>
            <input type="text" class="form-control" placeholder="Portal Number" name="portal_no" id="portal_no" 
                   value="{{ $next_cvrnum }}" 
                   style="background-color: #f0f0f0;" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Application Date</label>
            <input type="date" class="form-control" placeholder="Application Date" name="app_date" id="app_date" style="background-color: #f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Business Line</label>
            <input type="text" class="form-control" placeholder="Business Line" name="bus_line" id="bus_line" value="Marine" style="background-color: #f0f0f0;" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Business Class</label>
            <input type="text" class="form-control" placeholder="Business Class" name="bus_class" id="bus_class" 
                   value="{{ isset($openPolData[0]) ? $openPolData[0]->PBC_DESC : '' }}" 
                   style="background-color: #f0f0f0;" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Insured Name</label>
            <input type="text" class="form-control" placeholder="Insured Name" name="insured" id="insured" 
                   value="{{ isset($openPolData[0]) ? $openPolData[0]->PPS_DESC : '' }}" 
                   style="background-color: #f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Address</label>
            <textarea class="form-control" placeholder="Address" name="address" style="background-color: #f0f0f0;" readonly>{{ isset($openPolData[0]) ? $openPolData[0]->PAS_ADDRESS1 : '' }}</textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Category *</label>
            <select class="form-control" name="mar_catg" id="mar_catg" required> 
                <option disabled selected value="">Choose Category</option>
                <option value="Import">Import</option>
                <option value="Export">Export</option>
                <option value="Inland">Inland</option>
            </select>
        </div>

        <div class="form-group col-md-4">
            <label>Per Carry Limit</label>
            <input type="number" class="form-control" placeholder="Per Carry Limit" name="per_carry" id="per_carry" 
                   value="{{ isset($pol[0]) ? $pol[0]->per_carry : '' }}" readonly>
        </div>
    </div>

    <br>
    <h3> Consignment Information</h3>
    <br>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>PO No.</label>
            <input type="number" class="form-control" placeholder="PO No" name="po_no" id="po_no" 
                   value="{{ isset($pol[0]) ? $pol[0]->po_no : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Voyage/Consignment/Flight No.</label>
            <input type="number" class="form-control" placeholder="Voyage/Consignment/Flight No." name="voyage_no" id="voyage_no" 
                   value="{{ isset($pol[0]) ? $pol[0]->voyage_no : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Arrival Date</label>
            <input type="date" class="form-control" placeholder="Arrival Date" name="arr_date" id="arr_date" 
                   value="{{ isset($pol[0]) ? $pol[0]->arr_date : '' }}" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Invoice No.</label>
            <input type="number" class="form-control" placeholder="Invoice No" name="inv_no" id="inv_no" 
                   value="{{ isset($pol[0]) ? $pol[0]->inv_no : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Invoice Date</label>
            <input type="date" class="form-control" placeholder="Invoice Date" name="inv_date" id="inv_date" 
                   value="{{ isset($pol[0]) ? $pol[0]->inv_date : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Conveyance</label>
            <select class="form-control" name="conv" multiple> 
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
            <input type="number" class="form-control" placeholder="BL/AWB No" name="bl_no" id="bl_no" 
                   value="{{ isset($pol[0]) ? $pol[0]->bl_no : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>BL/AWB Date</label>
            <input type="date" class="form-control" placeholder="BL/AWB Date" name="bl_date" id="bl_date" 
                   value="{{ isset($pol[0]) ? $pol[0]->bl_date : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Packing</label>
            <select class="form-control" name="packingSelect" id="packingSelect">
                <option disabled selected value="">Choose Packing</option>
                <option value="Standard">In Standard Packing</option>
                <option value="Container">In Container Only</option>
                <option value="Other">Other</option>
            </select>
            <div id="otherPackingInput" style="display: none;">
                <label for="otherPackingText">Specify Packing</label>
                <input type="text" class="form-control" name="otherPackingText" id="otherPackingText">
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Voyage From</label>
            <input type="text" class="form-control" placeholder="Voyage From" name="voyage_from" id="voyage_from" 
                   value="{{ isset($pol[0]) ? $pol[0]->voyage_from : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Voyage To</label>
            <input type="text" class="form-control" placeholder="Voyage To" name="voyage_to" id="voyage_to" 
                   value="{{ isset($pol[0]) ? $pol[0]->voyage_to : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>VIA</label>
            <input type="text" class="form-control" placeholder="VIA" name="via" id="via" 
                   value="{{ isset($pol[0]) ? $pol[0]->via : '' }}" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Vessel / Carrier</label>
            <div class="searchable">
                <input type="text" autocomplete="off" list="vessels" id="vessel" name="vessel" class="form-control" 
                       placeholder="search vessel" onchange="updateVesselCode()" onkeyup="filterFunction(this,event)" 
                       value="{{ isset($pol[0]) ? $pol[0]->vessel : '' }}" readonly>
                <input type="hidden" id="vessel_code" name="vessel_code">
                <datalist id="vessels">
                    @foreach($vessels as $row)
                        <option value="{{ $row->PVC_CODE }}" data-code="{{ $row->PVC_CODE }}">{{ $row->PVC_DESC }}</option>
                    @endforeach
                </datalist>
            </div>
        </div>

        <div class="form-group col-md-4">
            <label>LC Number</label>
            <input type="number" class="form-control" placeholder="LC Number" name="lc_no" id="lc_no" 
                   value="{{ isset($pol[0]) ? $pol[0]->lc_no : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>LC Date</label>
            <input type="date" class="form-control" placeholder="LC Date" name="lc_date" id="lc_date" 
                   value="{{ isset($pol[0]) ? $pol[0]->lc_date : '' }}" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Bank *</label>
            <select class="form-control" name="bank" id="bank" required> 
                <option disabled selected value="">Choose Bank</option>
                <option value="12539">Al Baraka Islamic Bank Ltd</option>
                <option value="1150Y">Askari bank Limited Islamic Banking Services</option>
                <option value="11555">Askari Commercial Bank Ltd</option>
                <option value="11510">Askari bank Limited</option>
                <option value="115AS">Askari Islamic Bank Limited</option>
                <option value="20603">Askari Leasing Ltd</option>
                <option value="12503">Al-Baraka Islamic Bank</option>
                <option value="14401">Al-Faysal Investment Bank Ltd</option>
                <option value="10116">Allied Bank Limited</option>
                <option value="101CA">Allied Bank Limited (Islamic Banking)</option>
                <option value="114FH">Bank Al-Falah Ltd (Islamic)</option>
                <option value="11410">Bank Al-Falah Limited</option>
                <option value="11369">Bank Al Habib (Islamic)</option>
                <option value="11302">Bank Al Habib Limited</option>
                <option value="16002">Bank Islami Pakistan Limited</option>
                <option value="61601">Bank Makramah Limited</option>
                <option value="12801">Dubai Islamic Bank ltd</option>
                <option value="15307">Dubai Islamic Bank Pakistan ltd</option>
                <option value="18601">FINCA Microfinance Bank Limited</option>
                <option value="18601">Faysal Bank Limited (Islamic)</option>
                <option value="11101">Faysal Bank Limited</option>
                <option value="13201">Faysal Islamic Bank Limited</option>
                <option value="31009">First Habib Bank Modaraba</option>
                <option value="30603">First National Bank Modaraba</option>
                <option value="13510">First Women Bank Limited</option>
                <option value="17101">HSBC Bank Middle East Ltd</option>
                <option value="10801">Habib Bank Limited</option>
                <option value="15271">Habib Metro Bank Limited</option>
                <option value="15205">Habib Metropolitan Bank Limited</option>
                <option value="16189">J.S Bank Limited</option>
                <option value="10416">MCB Bank Limited</option>
                <option value="104VE">MCB Islami Bank Limited</option>
                <option value="13003">Meezan Bank Limited</option>
                <option value="12003">Metropolitan Bank Limited</option>
                <option value="18501">Micro Finance Apna Bank Limited</option>
                <option value="106DT">NBP Islamic</option>
                <option value="10610">National Bank of Pakistan</option>
                <option value="11201">SME Bank Limited</option>
                <option value="22703">Standard Chartered Leasing</option>
                <option value="17401">Samba Bank Limited</option>
                <option value="11826">Saudi Pak Commercial Bank Limited</option>
                <option value="17304">Silk Bank Limited</option>
                <option value="17903">Sindh Bank Limited</option>
                <option value="23401">Sindh Leasing Company Limited</option>
                <option value="12901">Soneri Bank Limited</option>
                <option value="10304">Standard Chartered LIMITED</option>
                <option value="17615">Summit Bank Limited</option>
                <option value="12202">The Bank Of Khyber</option>
                <option value="134AU">The Bank Of Punjab</option>
                <option value="18401">U Microfinance Bank Limited</option>
                <option value="10704">United Bank Limited</option>
            </select>
        </div>

        <div class="form-group col-md-4">
            <label>Incoterms *</label>
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
            <label>Salling/Depature/Dispatch on/or about</label>
            <input type="date" class="form-control" placeholder="Salling/Depature/Dispatch on/or about" name="salling" id="salling" 
                   value="{{ isset($pol[0]) ? $pol[0]->salling : '' }}" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Form E/Builty Number</label>
            <input type="number" class="form-control" placeholder="Form E/Builty Number" name="builty_no" id="builty_no" 
                   value="{{ isset($pol[0]) ? $pol[0]->builty_no : '' }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Builty Date</label>
            <input type="date" class="form-control" placeholder="Builty Date" name="builty_date" id="builty_date" 
                   value="{{ isset($pol[0]) ? $pol[0]->builty_date : '' }}" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Additional Terms (if any)</label>
            <textarea class="form-control" placeholder="Additional Terms (if any)" name="add_terms" readonly>{{ isset($pol[0]) ? $pol[0]->add_terms : '' }}</textarea>
        </div>

        <div class="form-group col-md-6">
            <label>Subject Matter Insured</label>
            <textarea class="form-control" placeholder="Subject Matter Insured" name="sub_mat" readonly>{{ isset($pol[0]) ? $pol[0]->sub_mat : '' }}</textarea>
        </div>
    </div>

    <br>
    <h3> Basis Of Valution </h3>
    <br>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Currency Type *</label>
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
            <input type="number" class="form-control" placeholder="Exchange Rate" name="ex_rate" id="ex_rate" 
                   value="{{ isset($pol[0]) ? $pol[0]->ex_rate : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Foreign Currency</label>
            <input type="number" class="form-control" placeholder="Foreign Currency" name="si_fc" id="si_fc" 
                   value="{{ isset($pol[0]) ? $pol[0]->si_fc : '' }}" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Incidental Charges</label>
            <div class="row">
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="%" name="inc_per" id="inc_per" 
                           value="{{ isset($pol[0]) ? $pol[0]->inc_per : '' }}" readonly>
                </div>
                <div class="col-md-6">
                    <input type="number" class="form-control" placeholder="Incidental Charges" name="inc_chrg" id="inc_chrg" 
                           value="{{ isset($pol[0]) ? $pol[0]->inc_chrg : '' }}" style="background-color: #f0f0f0;" readonly>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label>Tolerance</label>
            <div class="row">
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="%" name="tol_per" id="tol_per" 
                           value="{{ isset($pol[0]) ? $pol[0]->tol_per : '' }}" readonly>
                </div>
                <div class="col-md-6">
                    <input type="number" class="form-control" placeholder="Tolerance Charges" name="tolrence" id="tolrence" 
                           value="{{ isset($pol[0]) ? $pol[0]->tolrence : '' }}" style="background-color: #f0f0f0;" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Total Foreign Currency Value</label>
            <input type="number" class="form-control" placeholder="Total Foreign Currency Value" name="si_tfc" id="si_tfc" 
                   value="{{ isset($pol[0]) ? $pol[0]->si_tfc : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>

        <div class="form-group col-md-2"></div>

        <div class="form-group col-md-4">
            <label>Sum Insured (Rs)</label>
            <input type="number" class="form-control" placeholder="Sum Insured (Rs)" name="si_rs" id="si_rs" 
                   value="{{ isset($pol[0]) ? $pol[0]->si_rs : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Gross Premium</label>
            <input type="number" class="form-control" placeholder="Gross Premium" name="gross_pre" id="gross_pre" 
                   value="{{ isset($pol[0]) ? $pol[0]->gross_pre : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Admin</label>
            <input type="number" class="form-control" placeholder="Admin" name="admin_sur" id="admin_sur" 
                   value="{{ isset($pol[0]) ? $pol[0]->admin_sur : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Sub Total</label>
            <input type="number" class="form-control" placeholder="Sub Total" name="sub_total" id="sub_total" 
                   value="{{ isset($pol[0]) ? $pol[0]->sub_total : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Region *</label>
            <select class="form-control" name="region" id="region" onchange="calculateGSTAndStampDutyAndNet()" required> 
                <option disabled selected value="">Choose Region</option>
                <option value="Sindh">Sindh</option>
                <option value="Punjab">Punjab</option>
            </select>
        </div>

        <div class="form-group col-md-4">
            <label>GST</label>
            <input type="number" class="form-control" placeholder="GST" name="gst" id="gst" 
                   value="{{ isset($pol[0]) ? $pol[0]->gst : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>F.I.F</label>
            <input type="number" class="form-control" placeholder="F.I.F" name="fif" id="fif" 
                   value="{{ isset($pol[0]) ? $pol[0]->fif : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Stamp Duty</label>
            <input type="number" class="form-control" placeholder="Stamp Duty" name="stamp_duty" id="stamp_duty" 
                   value="{{ isset($pol[0]) ? $pol[0]->stamp_duty : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Net Premium</label>
            <input type="number" class="form-control" placeholder="Net Premium" name="net_pre" id="net_pre" 
                   value="{{ isset($pol[0]) ? $pol[0]->net_pre : '' }}" style="background-color: #f0f0f0;" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Document Description</label>
            <textarea class="form-control" placeholder="Document Description" name="doc_desc">{{ isset($pol[0]) ? $pol[0]->doc_desc : '' }}</textarea>
        </div>
    </div>

    @if(isset($invoiceData) && $invoiceData)
        <input type="hidden" name="invoice_id" value="{{ $invoiceData->id }}">
    @endif

    <!-- SUBMIT BUTTON SECTION -->
    <div class="form-row mt-4">
        <div class="form-group col-md-12">
            <div class="d-flex justify-content-between">
               
                <button type="submit" class="btn btn-primary btn-lg">
                    Submit
                </button>
            </div>
        </div>
    </div>
    <!-- END SUBMIT BUTTON SECTION -->

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