function floor(number)
{
	//return Math.floor(number * Math.pow(10, 2)) / Math.pow(10, 2);
	return number;
}

function removeFiller(txt)
{
	return txt.replace(/[^0-9\.]/gi, "");
}

function dosum()
{
	// Remove commas and dollar signs
	var price = removeFiller(document.getElementById('gd-mortgage-calculator-price').value); // Price
	var downPayment = removeFiller(document.getElementById('gd-mortgage-calculator-down-payment').value); // Down Payment
	var intrestRate = removeFiller(document.getElementById('gd-mortgage-calculator-interest').value); // Intrest Rate
	var years = removeFiller(document.getElementById('gd-mortgage-calculator-years').value); // Years
	var annualTax = removeFiller(document.getElementById('gd-mortgage-calculator-annual-taxes').value); // Annual Tax
	var insurance = removeFiller(document.getElementById('gd-mortgage-calculator-insurance').value); // Insurance
	var payment = removeFiller(document.getElementById('gd-mortgage-calculator-monthly-estimate').value); // Payment
	
	// Get annual tax rate
	//'mprice'

//'mtax'
	

	// Calculate insurance
	//insurance = price * .002;
	//insurance = 0;
	var monthlyIntrest = intrestRate / 1200;
	var base = 1;
	var mbase = 1 + monthlyIntrest;
	var xprice;

	for (i = 0; i < years * 12; i++)
		base = base * mbase;

	if(downPayment)
		xprice = price - downPayment;
	else
		xprice = price;

	monthlyPrincipalIntrest = floor(xprice * monthlyIntrest / ( 1 - (1 / base)));
	monthlyTax = floor(annualTax / 12);
	monthlyIntrest = floor(insurance / 12);
	payment =
		monthlyPrincipalIntrest +
		annualTax / 12 + 
		insurance / 12;
	payment = floor(payment);

	// Write to form
	document.getElementById('gd-mortgage-calculator-price').value = commify(price);
	document.getElementById('gd-mortgage-calculator-down-payment').value = commify(downPayment);
	document.getElementById('gd-mortgage-calculator-interest').value = intrestRate + '%';
	document.getElementById('gd-mortgage-calculator-annual-taxes').value  = commify(annualTax);   
	document.getElementById('gd-mortgage-calculator-insurance').value = commify(insurance);
	document.getElementById('gd-mortgage-calculator-monthly-estimate').value = commify(payment);
}

function commify(num)
{
	if(isNaN(num))
		num = "0";
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num * 100 + 0.50000000001);
	//cents = num % 100;
	num = Math.floor(num / 100).toString();
	//if(cents<10)
		//cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
	//return (((sign) ? '' : '-') + '$' + num + '.' + cents);
	if(num === 0)
		return (((sign) ? '' : '-') + '$');
	else
		return (((sign) ? '' : '-') + '$' + num);
}
