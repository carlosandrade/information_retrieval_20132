<html>
    <head>
        <title>PHP Teste</title>
    </head>
    <body >
	<div id="imagens">
			<div id="divBusca">
			<h2 id='titlesite'>FutebolNoticias</h2>
				<input type="text" id="txtBusca" placeholder="Digite aqui sua pesquisa"/>
			</div>
		<div id="checkbox">Tipo de campeonato<br>
			<input type="checkbox" name="copa" value="Milk">Campeonato brasileiro<br>
			<input type="checkbox" name="copa" value="Milk">Copa do mundo<br>
			<input type="checkbox" name="copa" value="Milk">Regionais<br>
		</div>
		<div id="mes">Mes<br>
			<select>
				  <option value="Janeiro"></option>
				  <option value="Janeiro">Janeiro</option>
				  <option value="Fevereiro">Fevereiro</option>
				  <option value="Março">Março</option>
				  <option value="Abril">Abril</option>
			</select>
		</div>
		<div id="ano">Ano<br>
			<select>
				  <option value=""></option>
				  <option value="2010">2010</option>
				  <option value="2011">2011</option>
				  <option value="2012">2012</option>
				  <option value="2013">2013</option>
				  <option value="2014">2014</option>
			</select>
		</div>
		<button id="btnBusca">Buscar</button>
	</div>
	
    </body>
</html>

<style type="text/css">

#checkbox{
	margin-top:7%;
	margin-left:5%;
	width:25%;
    height:3%
	
}

#mes{
	margin-top:-3%;
	margin-left:43%;
	width:20%;
    height:15%
}

#ano{
	margin-top:-6%;
	margin-left:65%;
	width:20%;
    height:15%
}

#txtBusca{
	margin-top:-1%;
	margin-left:2%;
	position: absolute;
	border:solid 1px #000000;
    width:18%;
    height:3%;   
}

#titlesite{
	margin-top:3%;
	margin-left:7%;
}

#imagens {
	margin-top:15%;
	margin-left:32%;
	border:solid 1px #000000;
    width:40%;
    height:30%;

}


#btnBusca{
	margin-top:15%;
	margin-left:80%;
    border:none;
    height:12%;
    border-radius:1% 7px 1% 7px;
    width:15%;
    font-weight:bold;
   
}
 
</style">