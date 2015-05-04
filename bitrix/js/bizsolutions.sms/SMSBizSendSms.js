function SendDeliveryForm(params) {
    this.message = $('#message');
    this.additionalPhoneNumbers = $('#additionalPhoneNumbers');

    this.isBitrixGroup = $('#isBitrixGroup');
    this.isSocialGroup = $('#isSocialGroup');
	this.isOrderGroup = $('#isOrderGroup');
    this.isAdditionalPhoneNumbers = $('#isAdditionalPhoneNumbers');

    this.phoneFieldBitrixGroup = $('#phone_field_bitrix_group');
	this.phoneFieldSocialGroup = $('#phone_field_social_group');

    //counters
    this.messageLength = $('#messageLength');
    this.partSize = $('#partSize');
    this.parts = $('#partsCount');

    if ($.trim(this.message.val()) == '') {
        this.message.val('');
    } else {
        this.message.val($.trim(this.message.val()));
    }
    if ($.trim(this.additionalPhoneNumbers.val()) == '') {
        this.additionalPhoneNumbers.val('');
    } else {
        this.additionalPhoneNumbers.val($.trim(this.additionalPhoneNumbers.val()));
    }
	this.Init();
}

SendDeliveryForm.prototype.Init = function() {
    $('#bitrixGroup').prop('disabled', !this.isBitrixGroup.prop("checked"));
	$('#phone_field_bitrix_group').prop('disabled', !this.isBitrixGroup.prop("checked"));
    $('#socialGroup').prop('disabled', !this.isSocialGroup.prop("checked"));
	$('#phone_field_social_group').prop('disabled', !this.isSocialGroup.prop("checked"));
	$('#orderGroupDateFrom_calendar_from').prop('disabled', !this.isOrderGroup.prop("checked"));
	$('#orderGroupDateTo_calendar_to').prop('disabled', !this.isOrderGroup.prop("checked"));
    $('#additionalPhoneNumbers').prop('disabled', !this.isAdditionalPhoneNumbers.prop("checked"));

    //this.isBitrixGroup.prop('disabled', $("#phone_field").val() == 'blank' ? true : false);
    //this.isSocialGroup.prop('disabled', $("#phone_field").val() == 'blank' ? true : false);
    //this.isAdditionalPhoneNumbers.prop('disabled', $("#phone_field").val() == 'blank' ? true : false);
    this.message.prop('disabled', $("#phone_field").val() == 'blank' ? true : false);



	this.InitEvents();
}

SendDeliveryForm.prototype.InitEvents = function() {
    var _this = this;

    var hideMess = false;
    this.message.click(function() {
        if (!hideMess && _this.message.hasClass('gray')) {
            _this.message.val('');
            hideMess=true;
        }
        $(this).removeClass('gray');
    });

    this.phoneFieldBitrixGroup.change(function() {
        if ($("#phone_field_bitrix_group").val() == 'blank') {
            $('#bitrixGroup').prop('disabled', true);
        } else {
            $('#bitrixGroup').prop('disabled', false);
        }
    });
	
	this.phoneFieldSocialGroup.change(function() {
        if ($("#phone_field_social_group").val() == 'blank') {
            $('#socialGroup').prop('disabled', true);
        } else {
            $('#socialGroup').prop('disabled', false);
        }
    });

    this.isBitrixGroup.change(function() {
		
        $('#bitrixGroup').prop(
			'disabled',
			this.checked ? ($("#phone_field_bitrix_group").val() == 'blank' ? true : false) : true
		);
		$('#phone_field_bitrix_group').prop('disabled', this.checked ? false : true);
    });

    this.isSocialGroup.change(function() {
        $('#socialGroup').prop(
			'disabled',
			this.checked ? ($("#phone_field_social_group").val() == 'blank' ? true : false) : true
		);
		$('#phone_field_social_group').prop('disabled', this.checked? false : true);
    });
	
	this.isOrderGroup.change(function() {
        $('#orderGroupDateFrom_calendar_from').prop('disabled', this.checked? false : true);
		$('#orderGroupDateTo_calendar_to').prop('disabled', this.checked? false : true);
    });

    this.isAdditionalPhoneNumbers.change(function() {
        $('#additionalPhoneNumbers').prop('disabled', this.checked? false : true);
    });

    this.message.keyup(function() {_this.Recount()});
}

SendDeliveryForm.prototype.Recount = function() {
    var text = this.message.val();
    if (text.match(/\r/g) == null) {
        var newLinesymbols = text.match(/\n/g);
        newLinesymbolsCount = (newLinesymbols != null)? newLinesymbols.length : 0;
    }

    textLength = text.length + newLinesymbolsCount;
    messLenPart = (isRus(text)) ? ((textLength) > 70 ? 66 : 70) : ((textLength) > 160 ? 153 : 160);

    var parts = Math.ceil(textLength / messLenPart);

    this.messageLength.text(textLength);
    this.partSize.text(messLenPart);
    this.parts.text(parts);
}

function isRus(text) {
    for (var d6 = 0; d6 < text.length; d6++) {
        if (
            text.charCodeAt(d6) > 126
            || text.charAt(d6) == '['
            || text.charAt(d6) == "]"
            || text.charAt(d6) == "\\"
            || text.charAt(d6) == "^"
            || text.charAt(d6) == "_"
            || text.charAt(d6) == "`"
            || text.charAt(d6) == "{"
            || text.charAt(d6) == "}"
            || text.charAt(d6) == "|"
            || text.charAt(d6) == "~"
        ) {
            return true;
        }
    }
    return false;
}


