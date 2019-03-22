<!DOCTYPE html>
<html>
	<head>
		<?php include 'inc/meta.php'; ?>
		<link rel="stylesheet" type="text/css" href="assets/css/tasks.css?v=<?=time()?>"/>
		<title>Woney | Tasks</title>
	</head>
	<body>

		<?php include 'inc/header.php'; ?>

		<main>

			<section id="body">
				
				<div class="wrapper">

					<div class="content">

						<div class="content-head">

							<div class="content-title" class="dark-tasks">Task List</div>

							<div class="content-btns">
								
								<div id="add-btn">Add</div>

							</div>

						</div>

						<div class="lines">

							<div class="line active">
							
								<div class="item">
									<div class="task-btn"><?=rand(10,24).':'.rand(0,60).'.'.rand(0,60)?></div>
								</div>

								<div class="item">
									<div class="task-title">mobil arama sonuçları medigodaki gibi olsun</div>
								</div>

								<div class="tags">
									<div style="background-color:#F1C40F;">Will Be Good</div>
								</div>

								<div class="btns">
									
									<div class="edit"></div>
									<div class="remove"></div>

								</div>

							</div>
						
							<?php for ($i=0; $i < 5; $i++) { ?>

							<div class="line">
								
								<div class="item">
									<div class="task-btn"><?=rand(10,24).':'.rand(0,60).'.'.rand(0,60)?></div>
								</div>

								<div class="item">
									<div class="task-title">404 sayfası hazırlanacak (sistemde olan sayfalar filtrelenecek [güvenlik update'i])</div>
								</div>

								<div class="tags">
									<div style="background-color:#9B59B6;">Heal2Go</div>
								</div>

								<div class="btns">
									
									<div class="edit"></div>
									<div class="remove"></div>

								</div>

							</div>

							<div class="line">
								
								<div class="item">
									<div class="task-btn"><?=rand(10,24).':'.rand(0,60).'.'.rand(0,60)?></div>
								</div>

								<div class="item">
									<div class="task-title">Heal2GO *Watch Our Video* Doctor's Recommend Us Video Making *Phase2nd</div>
								</div>

								<div class="tags">
									<div style="background-color:#F1C40F;">Will Be Good</div>
								</div>

								<div class="btns">
									
									<div class="edit"></div>
									<div class="remove"></div>

								</div>

							</div>

							<div class="line">
								
								<div class="item">
									<div class="task-btn"><?=rand(10,24).':'.rand(0,60).'.'.rand(0,60)?></div>
								</div>

								<div class="item">
									<div class="task-title">Panel bölümünde "Doctors-About) segmesi için adım adım description ekleme </div>
								</div>

								<div class="tags">
									<div style="background-color:#2C3E50;">Risus</div>
								</div>

								<div class="btns">
									
									<div class="edit"></div>
									<div class="remove"></div>

								</div>

							</div>

							<?php } ?>

						</div>

					</div>

				</div>

			</section>

		</main>

	</body>
</html>