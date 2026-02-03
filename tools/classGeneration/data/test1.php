Dubi
{
	class: Dubi 
	table: dubi
	column: dubiId; type: text; property: dubiId; role: primary
	column: firstname; type: text; property: firstname; role: value
	column: lastname; type: text; property: lastname; role: others
	column: extraFilter; type: text; property: extraFilter; role: others
	column: extraInformation; type: text; property: extraInformation; role: others
	column: flags; type: text; property: flags; role: others
}
Ivan
{
	class: Ivan
	table: ivan
	column: ivanId; type: text; property: ivanId; role: primary
	column: dubiId; type: object; property: dubi, Dubi; role: others
	column: extraFilter; type: text; property: extraFilter; role: others
	column: extraInformation; type: text; property: extraInformation; role: others
	column: flags; type: text; property: flags; role: others
}
End_Class