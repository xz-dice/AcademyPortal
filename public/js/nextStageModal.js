function updateStage(url, applicantId, btnNextStage) {
    fetch(url).then(
        function(response) {
            response.json().then(
                function(data) {
                    document.querySelector('#currentStageName' + applicantId).innerHTML = data['data']['newStageName'];
                    btnNextStage.dataset.stageid =  data['data']['stageId'];
                    if (data['data']['isLastStage'] === data['data']['stageId']) {
                        $(btnNextStage).remove();
                    }
                }
            )
        }
    )
}

$(document).ready(function(){
    $(".btnNextStage").click(function(){
        const stageId = this.dataset.stageid;
        const applicantId = this.dataset.applicantid;
        const thisButton  = this;
        var url = './api/getNextStageOptions/' + stageId;
        fetch(url)
            .then(
                function(response) {
                    if (response.status !== 200) {
                        document.querySelector('#modal-main').innerHTML = '';
                        document.querySelector('#modal-main').innerHTML += '<div class="alert alert-danger" role="alert">Looks like there was a problem. Status Code: ' + response.status + '</div>';
                        return;
                    }
                    response.json().then(function(data) {
                        if (parseInt(data.data.nextStageId) === 6) {
                            console.log('hello');
                            $('#studentPasswordModal').modal('show');
                            fetch(`/api/applicantPassword/${applicantId}`, {
                                method: 'POST'
                            }).then(data => data.json())
                                .then(passwordResponse => {
                                    console.log(passwordResponse)
                                })

                            var url = './api/progressApplicantStage?stageId=' + data['data']['nextStageId'] + '&applicantId=' + applicantId;
                            updateStage(url, applicantId, thisButton)
                            document.querySelector('.btnSavePassword').addEventListener('click',
                                function() {
                                    window.location.reload();
                                    $('#studentPasswordModal').modal('hide');
                                })
                        } else if (data['data']['nextStageOptions'].length === 0) {
                            var url = './api/progressApplicantStage?stageId=' + data['data']['nextStageId'] + '&applicantId=' + applicantId;
                            updateStage(url, applicantId, thisButton);
                        } else {
                        document.querySelector('#next-stage-options').innerHTML = '<option>Please select an Option</option>';
                        let optionValues = "";
                        data['data']['nextStageOptions'].forEach(item => {
                            optionValues += `<option value="${item.id}">${item.option}</option>`;
                        })
                        document.querySelector('#next-stage-options').innerHTML += optionValues;
                        $('#nextStageModal').modal('show');
                        document.querySelector('.btnNextStageOptions').addEventListener('click',
                            function() {
                                    const optionId =  document.querySelector('#next-stage-options').value;
                                    var url = './api/progressApplicantStage?stageId=' + data['data']['nextStageId'] + '&applicantId=' + applicantId + '&optionId=' + optionId;
                                    updateStage(url, applicantId, thisButton)
                                    window.location.reload();
                                    $('#nextStageModal').modal('hide');
                            }
                            )
                        }
                    })
                }
            )
        }
    )
})
