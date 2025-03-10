var Questboard = {
  dismissNewQuestAlert: function (bburl, id) {
    var questboardAlert = $("#new-quest");
    if (!questboardAlert.length) {
      return false;
    }

    if (use_xmlhttprequest != 1) {
      return true;
    }

    $.ajax(
		{
			type: 'post',
			url: bburl + `questboard.php?action=questboard_read&read=${id}`,
			data: { ajax: 1, questboard_new: 1, uid: id },
			async: true
		});
		questboardAlert.remove();
		return false;
  },

  dismissNewQuestRegistrationAlert: function (bburl, id) {
    var questboardAlert = $("#new-registration");
    if (!questboardAlert.length) {
      return false;
    }

    if (use_xmlhttprequest != 1) {
      return true;
    }

    $.ajax(
		{
			type: 'post',
			url: bburl + `questboard.php?action=questboard_registration_read&read=${id}`,
			data: { ajax: 1, questboard_new_registration: 1, uid: id },
			async: true
		});
		questboardAlert.remove();
		return false;
  },

  dismissNewQuestEvaluationAlert: function (bburl, id) {
    var questboardAlert = $("#new-evaluation");
    console.log('BLUUUBB')
    if (!questboardAlert.length) {
      return false;
    }

    if (use_xmlhttprequest != 1) {
      return true;
    }
    console.log('TEEEEEST')

    $.ajax(
		{
			type: 'post',
			url: bburl + `questboard.php?action=questboard_evaluation_read&read=${id}`,
			data: { ajax: 1, questboard_quest_evaluation: 1, uid: id },
			async: true
		});
		questboardAlert.remove();
		return false;
  },
};
