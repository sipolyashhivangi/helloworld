var imgNumber = 4;
var selectionClicked = false;

function changeImage() {
	imgNumber++;
	if ( imgNumber > 4 ) {
		imgNumber = 1;
	}
	//var randomImage = Math.round(Math.random()*3) + 1
	$('#imgChange').attr( "src", "images/horseshoeDemo/myscore_horseshoe" + imgNumber + ".png");
	/*swap();*/
}

function swap(){
	var randomImageNumber = Math.round(Math.random()*3) + 1
	var newimg = "images/horseshoeDemo/myscore_horseshoe" + randomImageNumber + ".png";
	newimg = "images/horseshoeDemo/myscore_horseshoe4.png";
  $('#nextimg').attr('src',newimg );
/*$('#currentimg').fadeOut('slow',
  		function(){
  			$(this).attr('src',$('#nextimg').attr('src')).fadeIn();
  		}
  	);*/
   $('#currentimg').fadeOut( 500 );
}

function initButtons() {
	/* normal buttons */
	$('.recButton').hover( 
		function() {
			$(this).removeClass( 'recButton recButtonMouseDown' );
			$(this).addClass( 'recButtonHover' );
		},
		function() {
			$(this).removeClass( 'recButtonHover recButtonMouseDown' );
			$(this).addClass( 'recButton' );
		});

	$('.msdwn').mousedown( function() {
		$(this).removeClass( 'recButtonHover' );
		$(this).addClass( 'recButtonMouseDown' );
	});

	$('.msdwn').mouseup( function() {
		$(this).removeClass( 'recButtonMouseDown' );
		$(this).addClass( 'recButtonHover' );
	});


	/* large buttons */
	$('.recButtonDarkLarge').hover( 
	function() {
		if ( ! $(this).hasClass( 'btnclicked' ) ) {
			$(this).removeClass( 'recButtonDarkLarge recButtonDarkLargeMouseDown' );
			$(this).addClass( 'recButtonDarkLargeHover' );
		}
	},
	function() {
		if ( ! $(this).hasClass( 'btnclicked' ) ) {
			$(this).removeClass( 'recButtonDarkLargeHover recButtonDarkLargeMouseDown' );
			$(this).addClass( 'recButtonDarkLarge' );
		}

	});

	$('.recButtonDarkLarge2Line').hover( 
	function() {
		if ( ! $(this).hasClass( 'btnclicked' ) ) {
			$(this).removeClass( 'recButtonDarkLarge2Line recButtonDarkLargeMouseDown' );
			$(this).addClass( 'recButtonDarkLargeHover2Line' );
		}
	},
	function() {
		if ( ! $(this).hasClass( 'btnclicked' ) ) {
			$(this).removeClass( 'recButtonDarkLargeHover2Line recButtonDarkLargeMouseDown' );
			$(this).addClass( 'recButtonDarkLarge2Line' );
		}
	});

	$('.msdwnLarge').mousedown( function() {
		// 'click' this button, set all others to normal state
		$('.recButtonDarkLargeMouseDown').each( function() {
			//if ( ! $(this).hasClass( 'btnclicked' ) ) {
				$(this).removeClass( 'recButtonDarkLargeMouseDown' );
				$(this).addClass( 'recButtonDarkLarge' );
			//}
		});
		$('.recButtonDarkLargeMouseDown2Line').each( function() {
			//if ( ! $(this).hasClass( 'btnclicked' ) ) {
				$(this).removeClass( 'recButtonDarkLargeMouseDown2Line' );
				$(this).addClass( 'recButtonDarkLarge2Line' );
			//}
		});

		$(this).removeClass( 'recButtonDarkLargeHover' );
		$(this).addClass( 'recButtonDarkLargeMouseDown' );
		$(this).addClass( 'btnclicked' );
		selectionClicked = true;
	})

	$('.msdwnLarge').mouseup( function() {
		if ( !selectionClicked ) {
			$(this).removeClass( 'recButtonDarkLargeMouseDown' );
			$(this).addClass( 'recButtonDarkLargeHover' );
		}
	})

	$('.msdwnLarge2Line').mousedown( function() {
		// 'click' this button, set all others to normal state
		$('.recButtonDarkLargeMouseDown').each( function() {
			//if ( ! $(this).hasClass( 'btnclicked' ) ) {
				$(this).removeClass( 'recButtonDarkLargeMouseDown' );
				$(this).addClass( 'recButtonDarkLarge' );
			//}
		});
		$('.recButtonDarkLargeMouseDown2Line').each( function() {
			//if ( ! $(this).hasClass( 'btnclicked' ) ) {
				$(this).removeClass( 'recButtonDarkLargeMouseDown2Line' );
				$(this).addClass( 'recButtonDarkLarge2Line' );
			//}
		});

		$(this).removeClass( 'recButtonDarkLargeHover2Line' );
		$(this).addClass( 'recButtonDarkLargeMouseDown2Line' );
		$(this).addClass( 'btnclicked' );
		selectionClicked = true;
	})

	$('.msdwnLarge2Line').mouseup( function() {
		if ( !selectionClicked ) {
			$(this).removeClass( 'recButtonDarkLargeMouseDown2Line' );
			$(this).addClass( 'recButtonDarkLargeHover2Line' );
		}
	})
}

$(function() {
	initButtons();
});