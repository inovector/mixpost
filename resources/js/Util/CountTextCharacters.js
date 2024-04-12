import {parse as extractEmojiWithIndices} from 'twemoji-parser';
import extractUrlsWithIndices from 'twitter-text/dist/extractUrlsWithIndices';

const getLength = (text, config = []) => {
    const $config = {
        ...{
            urlWeight: null, // Weight for URLs. If null, URLs will be weighted as regular characters
            emojiWeight: 2, // Weight for emojis. If null, emojis will be weighted as regular characters
        },
        ...config
    };

    if (typeof text !== 'string') {
        throw new TypeError('Invalid input: text must be a string.');
    }

    const chars = text.split(''); // Split text into an array of characters
    let weightedLength = 0;

    const urlEntitiesMap = $config.urlWeight ? extractUrlsWithIndices(text) : [];
    const emojiEntitiesMap = $config.emojiWeight ? transformEntitiesToHash(extractEmojiWithIndices(text)) : [];
    const invalidCharRegex = /[^\u0009-\u000D\u0020-\uFFFF]/u; // Extended Unicode escape for wider character support

    for (let i = 0; i < chars.length; i++) {
        const char = chars[i];

        // Validate for invalid characters
        if (invalidCharRegex.test(char)) {
            continue;
        }

        // Calculate weight based on character type
        if ($config.urlWeight && urlEntitiesMap[i]) {
            const urlEntityMapCharacter = urlEntitiesMap[i],
                url = urlEntityMapCharacter.url;

            weightedLength += $config.urlWeight;
            i += url.length - 1;
        } else if ($config.emojiWeight && emojiEntitiesMap[i]) {
            const emojiEntityMapCharacter = emojiEntitiesMap[i],
                emoji = emojiEntityMapCharacter.text;

            weightedLength += $config.emojiWeight;
            i += emoji.length - 1; // Skip the next characters
        } else {
            weightedLength += 1; // Regular character
        }
    }

    return weightedLength;
};

const transformEntitiesToHash = (entities) => {
    return entities.reduce(function (map, entity) {
        map[entity.indices[0]] = entity;
        return map;
    }, {});
};

export default {
    getLength
}
