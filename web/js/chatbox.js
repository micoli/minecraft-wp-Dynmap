componentconstructors['chatbox'] = function(dynmap, configuration) {
	var me = this;
	
	if(dynmap.getBoolParameterByName("hidechat"))
		return;
	var chat = $('<div/>')
		.addClass('chat')
		.appendTo(dynmap.options.container);
	var messagelist = $('<div/>')
		.addClass('messagelist')
		.appendTo(chat);

	if (configuration.visiblelines) {
		messagelist.css('max-height', configuration.visiblelines + 'em');
	}
	else {
		messagelist.css('max-height', '6em');
	}	

	if (configuration.scrollback) {
		messagelist.addClass('scrollback')
			.click( function() { $(this).hide(); } );		 
	}

	if (dynmap.options.allowwebchat) {
	  if(dynmap.options.loggedin || !dynmap.options['webchat-requires-login']) {
		var chatinput = $('<input/>')
			.addClass('chatinput')
			.attr({
				id: 'chatinput',
				type: 'text',
				value: ''
			})
			.keydown(function(event) {
				if (event.keyCode == '13') {
					event.preventDefault();
					if(chatinput.val() != '') {
						$(dynmap).trigger('sendchat', [chatinput.val()]);
						chatinput.val('');
					}
				}
			});
		if(configuration.sendbutton) {
			var chatbutton = $('<button/>').addClass('chatsendbutton').click(function(event) {
			  if(chatinput.val() != '') {
				$(dynmap).trigger('sendchat', [chatinput.val()]);
				chatinput.val('');
			  }
			}).text("+").appendTo(chat);
		}
		chatinput.appendTo(chat);
	  }
	  else {
	  	var login = $('<button/>').addClass('loginbutton').click(function(event) {
	  		window.location = 'login.html';
	  	}).text(dynmap.options['msg-chatrequireslogin']).appendTo(chat);
	  }
		if (configuration.scrollback) {
			chatinput.click(function(){ 
				var m = $('.messagelist');
				m.show().scrollTop(m.scrollHeight());
			});
		}
	}
	
	var addrow = function(row) {
		if (configuration.scrollback) {
			var c = messagelist.children();
			c.slice(0, Math.max(0, c.length-configuration.scrollback)).each(function(index, elem){ $(elem).remove(); });
		} else {
			setTimeout(function() { row.remove(); }, (configuration.messagettl * 1000));
		}
		messagelist.append(row);
		messagelist.show();
		messagelist.scrollTop(messagelist.scrollHeight());
	};
	
	$(dynmap).bind('playerjoin', function(event, playername) {
		addrow($('<div/>')
			.addClass('messagerow')
			.text(dynmap.options.joinmessage.replace('%playername%', playername))
			);
	});
	
	$(dynmap).bind('playerquit', function(event, playername) {
		addrow($('<div/>')
			.addClass('messagerow')
			.text(dynmap.options.quitmessage.replace('%playername%', playername))
			);
	});
	
	$(dynmap).bind('chat', function(event, message) {
		var playerName = message.name;
		var playerAccount = message.account;
		var messageRow = $('<div/>')
			.addClass('messagerow');

		var playerIconContainer = $('<span/>')
			.addClass('messageicon');

		if (message.source === 'player' && configuration.showplayerfaces &&
			playerAccount) {
			getMinecraftHead(playerAccount, 16, function(head) {
				messageRow.icon = $(head)
					.addClass('playerMessageIcon')
					.appendTo(playerIconContainer);
			});
		}

		var playerChannelContainer = '';
		if (message.channel) {
			playerChannelContainer = $('<span/>').addClass('messagetext')
			.text('[' + message.channel + '] ')
			.appendTo(messageRow);
		}
			
		if (message.source === 'player' && configuration.showworld) {
			var playerWorldContainer = $('<span/>')
			 .addClass('messagetext')
			 .text('['+dynmap.players[message.name].location.world.name+']')
			 .appendTo(messageRow);
		}

		var playerNameContainer = '';
		if(message.name) {
			playerNameContainer = $('<span/>').addClass('messagetext').text(' '+message.name+': ');
		}
		
		var playerMessageContainer = $('<span/>')
			.addClass('messagetext')
			.text(chat_encoder(message));

		messageRow.append(playerIconContainer,playerChannelContainer,playerNameContainer,playerMessageContainer);
		addrow(messageRow);
	});
};
