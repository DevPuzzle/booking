<template>
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Deleting: {{leaveDay.summary}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>You are about to delete this leave event.</p>
                        <p>Operation is irreversible.</p>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-group m-auto" role="group" aria-label="Basic example">
                            <button type="submit" class="btn btn-danger" data-dismiss="modal"
                                    @click="$emit('delete')">Proceed
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>

    export default {
      props: {
        leaveDay: {}
        },
      methods: {}
    }
</script>
