const usePostVersions = () => {
    const versionContentObject = (body = '', media = []) => {
        return {
            body,
            media
        };
    }

    const versionObject = (accountId = 0, isOriginal = false) => {
        return {
            account_id: accountId,
            is_original: isOriginal,
            content: [versionContentObject()]
        }
    }

    const getOriginalVersion = (versions) => {
        const find = versions.find((version) => {
            return version.is_original && version.account_id === 0;
        })

        return find ? find : null;
    }

    const getAccountVersion = (versions, accountId) => {
        const find = versions.find((version) => {
            return version.account_id === accountId;
        })

        return find ? find : null;
    }

    const getIndexAccountVersion = (versions, accountId) => {
        return versions.findIndex(version => version.account_id === accountId);
    }

    return {
        versionContentObject,
        versionObject,
        getOriginalVersion,
        getAccountVersion,
        getIndexAccountVersion
    }
}

export default usePostVersions;
