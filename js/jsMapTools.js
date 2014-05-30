//get the color for points based on its sinr value
function getColor(number){
	if(number <= -5 )
		return 'blue';
	else if(number >-5&& number<=0 )
		return '#4169E1';
	else if(number >0&& number<=5 )
        return '#00FFFF';	
	else if(number >5&& number<=10 )
        return '#00FF00';	
	else if(number >10&& number<=15 )
        return '#FFFF00';
	else if(number >15&& number<=20 )
        return '#FA8072';	
	else if(number >20&& number<=25 )
        return '#FF4500';	
	else if(number >25&& number<=30 )
        return 'red';
	else
		return '#DC143C';
}