<template>
	<div class="card_box">
		<div class="card">
			<div class="pad_wrapper">
				<h1 class="div_underlined">Административен панел</h1>
				<p>Добре дошли в административния панел</p>
				<p>От тук може да менежирате товари , транспорт , потребители и фирми</p>
				<p>Другото за което се използва административния панел е</p>
				<p>да разглеждате различните статистики за всички обекти</p>
				<ul class="list_actions mt-20">
			        <li><router-link to="admin/users" class="btn small">Потребители</router-link></li>
			        <li><router-link to="admin/firms" class="btn small">Фирми</router-link></li>
			        <li><router-link to="admin/tovari" class="btn small">Товари</router-link></li>
			        <li><router-link to="admin/transport" class="btn small">Транспорт</router-link></li>
			    </ul>
			</div>
		</div>
	</div>
</template>
<script>
	export default {

	}
</script>