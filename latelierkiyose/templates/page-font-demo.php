<?php
/**
 * Template Name: Démo Polices
 * Description: Page de comparaison visuelle entre Lora (actuel) et Playfair Display (nouveau) pour les titres
 *
 * @package Kiyose
 */

get_header();
?>

<main id="main" class="site-main font-demo-page" role="main">
	<div class="container">
		<header class="page-header">
			<h1 class="page-title">Comparaison des polices de titres</h1>
			<p class="page-intro">Cette page permet de comparer visuellement la police actuelle (Lora, serif élégante) avec la nouvelle police proposée (Playfair Display, manuscrite chaleureuse).</p>
		</header>

		<!-- Section 1: Lora (actuelle) -->
		<section class="font-demo-section">
			<div class="section-header">
				<h2 class="section-title">Police actuelle : Lora</h2>
				<p class="section-description">Police serif élégante et traditionnelle, utilisée actuellement pour tous les titres du site.</p>
			</div>
			<div class="demo-content demo-lora">
				<h1>Bienvenue à l'Atelier Kiyose</h1>
				<p class="subtitle">Révélez votre lumière intérieure</p>

				<h2>Art-thérapie et Créativité</h2>
				<p>Libérez votre potentiel créatif à travers l'art-thérapie, la danse, le théâtre et les arts plastiques.</p>

				<h3>Ateliers de Rigologie</h3>
				<p>Découvrez le yoga du rire et les techniques psychocorporelles pour retrouver joie et équilibre.</p>

				<h4>Bols Tibétains</h4>
				<p>Sonothérapie et rééquilibrage énergétique par les vibrations des bols chantants.</p>

				<h5>Ateliers Philosophie</h5>
				<p>Débats démocratiques pour enfants et adolescents, cultiver l'esprit critique.</p>

				<h6>Témoignages</h6>
				<p>Découvrez les retours d'expérience de nos participants.</p>
			</div>
		</section>

		<!-- Section 2: Playfair Display (proposée) -->
		<section class="font-demo-section">
			<div class="section-header">
				<h2 class="section-title">Police proposée : Playfair Display</h2>
				<p class="section-description">Police serif display moderne avec fort contraste et caractère affirmé. Élégante et sophistiquée sans être académique ou trop sérieuse.</p>
			</div>
			<div class="demo-content demo-kalam">
				<h1>Bienvenue à l'Atelier Kiyose</h1>
				<p class="subtitle">Révélez votre lumière intérieure</p>

				<h2>Art-thérapie et Créativité</h2>
				<p>Libérez votre potentiel créatif à travers l'art-thérapie, la danse, le théâtre et les arts plastiques.</p>

				<h3>Ateliers de Rigologie</h3>
				<p>Découvrez le yoga du rire et les techniques psychocorporelles pour retrouver joie et équilibre.</p>

				<h4>Bols Tibétains</h4>
				<p>Sonothérapie et rééquilibrage énergétique par les vibrations des bols chantants.</p>

				<h5>Ateliers Philosophie</h5>
				<p>Débats démocratiques pour enfants et adolescents, cultiver l'esprit critique.</p>

				<h6>Témoignages</h6>
				<p>Découvrez les retours d'expérience de nos participants.</p>
			</div>
		</section>

		<!-- Section 3: Comparaison côte à côte -->
		<section class="font-demo-section">
			<div class="section-header">
				<h2 class="section-title">Comparaison directe</h2>
				<p class="section-description">Visualisez les deux polices côte à côte sur les mêmes contenus.</p>
			</div>
			<div class="demo-comparison">
				<div class="comparison-col">
					<h3 class="comparison-label">Lora (actuelle)</h3>
					<div class="demo-content demo-lora">
						<h1>L'Atelier Kiyose</h1>
						<h2>Art-thérapie</h2>
						<h3>Rigologie</h3>
						<h4>Bols Tibétains</h4>
						<h5>Philosophie</h5>
						<h6>Témoignages</h6>
					</div>
				</div>
				<div class="comparison-col">
					<h3 class="comparison-label">Playfair Display (proposée)</h3>
					<div class="demo-content demo-kalam">
						<h1>L'Atelier Kiyose</h1>
						<h2>Art-thérapie</h2>
						<h3>Rigologie</h3>
						<h4>Bols Tibétains</h4>
						<h5>Philosophie</h5>
						<h6>Témoignages</h6>
					</div>
				</div>
			</div>
		</section>

		<!-- Section 4: Contextes d'utilisation -->
		<section class="font-demo-section">
			<div class="section-header">
				<h2 class="section-title">Contextes d'utilisation réels</h2>
				<p class="section-description">Comment les polices apparaissent dans différents contextes du site.</p>
			</div>

			<div class="demo-contexts">
				<!-- Homepage Hero -->
				<div class="context-example">
					<h4 class="context-label">Homepage Hero (h1)</h4>
					<div class="context-content context-hero">
						<div class="context-variant demo-lora">
							<h1>Bienvenue à l'Atelier Kiyose</h1>
							<p class="subtitle">Révélez votre lumière intérieure</p>
						</div>
						<div class="context-variant demo-kalam">
							<h1>Bienvenue à l'Atelier Kiyose</h1>
							<p class="subtitle">Révélez votre lumière intérieure</p>
						</div>
					</div>
				</div>

				<!-- Service Cards -->
				<div class="context-example">
					<h4 class="context-label">Cartes de services (h3)</h4>
					<div class="context-content context-cards">
						<div class="context-variant demo-lora">
							<h3>Art-thérapie</h3>
							<h3>Rigologie</h3>
							<h3>Bols Tibétains</h3>
						</div>
						<div class="context-variant demo-kalam">
							<h3>Art-thérapie</h3>
							<h3>Rigologie</h3>
							<h3>Bols Tibétains</h3>
						</div>
					</div>
				</div>

				<!-- Blog Titles -->
				<div class="context-example">
					<h4 class="context-label">Titres d'articles (h2)</h4>
					<div class="context-content context-blog">
						<div class="context-variant demo-lora">
							<h2>Les bienfaits de l'art-thérapie sur le stress</h2>
							<h2>Découvrir le yoga du rire : un chemin vers la joie</h2>
						</div>
						<div class="context-variant demo-kalam">
							<h2>Les bienfaits de l'art-thérapie sur le stress</h2>
							<h2>Découvrir le yoga du rire : un chemin vers la joie</h2>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Instructions -->
		<section class="font-demo-section demo-instructions">
			<div class="section-header">
				<h2 class="section-title">Comment décider ?</h2>
			</div>
			<div class="instructions-content">
				<h3>Points à évaluer :</h3>
				<ul>
					<li><strong>Identité de marque</strong> : Quelle police correspond le mieux à l'esprit de L'Atelier Kiyose ? (créativité, chaleur, accessibilité)</li>
					<li><strong>Lisibilité</strong> : Les deux polices sont-elles lisibles à toutes les tailles (h1 à h6) ?</li>
					<li><strong>Cohérence</strong> : Comment la police s'intègre-t-elle avec le reste du design (Nunito pour le corps de texte) ?</li>
					<li><strong>Émotion</strong> : Quelle police transmet le mieux les valeurs de bienveillance, créativité et transformation ?</li>
				</ul>

				<h3>Options disponibles :</h3>
				<ul>
					<li><strong>Valider Playfair Display</strong> : Adopter la nouvelle serif display pour une identité élégante, moderne et affirmée</li>
					<li><strong>Conserver Lora</strong> : Garder la police actuelle pour son élégance classique et sobre</li>
					<li><strong>Tester une autre police</strong> : Explorer d'autres alternatives serif (Crimson Text, Cormorant Garamond, Libre Baskerville)</li>
				</ul>

				<p class="note"><strong>Note technique :</strong> Lora sera conservée en fallback même si Playfair Display est adoptée, garantissant la continuité visuelle en cas de problème de chargement.</p>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
