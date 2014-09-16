/*
Classes:
	CValidate
*/

function CValidate()
{
}

CValidate.prototype = 
{
    IsEmpty : function (strValue)
    {
        if(strValue.replace(/\s+/g,'') == '')
        {
            return true;
        }
        return false;
    },
    
    HasEmailForbiddenSymbols : function (strValue)
    {
        if(strValue.match(/[^A-Z0-9\"!#\$%\^\{\}`~&'\+-=_@\.]/i))
        {
            return true;
        }
        return false;
    },
    
    IsCorrectEmail : function (strValue)
    {
        if(   strValue.match(/^[A-Z0-9\"!#\$%\^\{\}`~&'\+-=_\.]+@[A-Z0-9\.-]+$/i)  )
        {
            return true;
        }
        return false;
    },
    
    IsCorrectServerName : function (strValue)
    {
        if(!strValue.match(/[^A-Z0-9\.-]/i))
        {
            return true;
        }
        return false;
    },
    
    IsPositiveNumber : function (intValue)
    {
        if(isNaN(intValue) || intValue <= 0 || Math.round(intValue) != intValue)
        {
            return false;
        }
        return true;
    },
    
    IsPort : function (intValue)
    {
        if(this.IsPositiveNumber(intValue) && intValue <= 65535)
        {
            return true;
        }
        return false;
    },
    
    HasSpecSymbols : function (strValue)
    {
        if(strValue.match(/[\"\/\\\*\?<>\|:]/))
        {
            return true;
        }
        return false;
    },
    
    IsCorrectFileName : function (strValue)
    {
        if(!this.HasSpecSymbols(strValue))
        {
            if(strValue.match(/^(CON|AUX|COM1|COM2|COM3|COM4|LPT1|LPT2|LPT3|PRN|NUL)$/i))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return false;
    },
    
    CorrectWebPage : function (strValue)
    {
        return strValue.replace(/^[\/;<=>\[\\#\?]+/g,'');
    },
    
    HasFileExtention : function (strValue, strExtension)
    {           
        if( strValue.substr(strValue.length - strExtension.length - 1,strExtension.length + 1).toLowerCase() == '.'+strExtension.toLowerCase())
        {
            return true;
        }
        return false;
    }
    
};
