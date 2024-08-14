import axios from 'axios';
import { Tag } from './interfaces';

export const getTagSelectItems = async (): Promise<Tag[]> => {
  const result = [];
  await axios.get(route('tags.items.select'))
    .then(response => {
      result.push(...response.data);
    })
    .catch(error => {
      console.log(error);
    });
  return result;
};