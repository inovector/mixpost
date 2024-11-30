import {computed, inject} from "vue";
import {isEmpty} from "lodash";

const usePostValidator = () => {
    const postCtx = inject('postCtx');

    const errors = computed(() => {
        return postCtx.errors;
    })

    const validationPassed = computed(() => {
        return isEmpty(errors.value);
    });

    const addError = ({group, key, message}) => {
        if (!postCtx.errors[group]) {
            postCtx.errors[group] = {};
        }

        postCtx.errors[group][key] = message;
    }

    const addAccountError = ({group, key, message, accountId, accountName, providerName}) => {
        let msg = providerName ? `${providerName}` : '';
        msg += accountName ? ` (${accountName})` : '';
        msg += message ? ` → ${message}` : '';

        addError({
            group,
            key: `${accountId}_${key}`,
            message: msg
        });
    }

    const removeError = ({group, key = null}) => {
        if (!postCtx.errors || !postCtx.errors[group]) {
            return;
        }

        if (!key) {
            delete postCtx.errors[group];
            return;
        }

        if (postCtx.errors[group][key]) {
            delete postCtx.errors[group][key];
        }

        if (isEmpty(postCtx.errors[group])) {
            delete postCtx.errors[group];
        }
    }

    const removeAccountError = ({group, key, accountId}) => {
        removeError({
            group,
            key: `${accountId}_${key}`
        });
    }

    const clearErrors = () => {
        postCtx.errors = {};
    }

    return {
        errors,
        validationPassed,
        addError,
        addAccountError,
        removeError,
        removeAccountError,
        clearErrors,
    }
}

export default usePostValidator;
